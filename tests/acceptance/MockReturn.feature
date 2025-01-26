Feature: MockReturn

  Background:
    Given I have the following config
      """
      <?xml version="1.0"?>
      <psalm errorLevel="1" findUnusedCode="false">
        <projectFiles>
          <directory name="."/>
          <ignoreFiles> <directory name="../../vendor"/> </ignoreFiles>
        </projectFiles>
        <plugins>
          <pluginClass class="Psalm\MockeryPlugin\Plugin"/>
        </plugins>
      </psalm>
      """
    And I have the following code preamble
      """
      <?php
      namespace NS;
      use Mockery;

      """

  Scenario: Defined method mocking sets proper intersection return type
    Given I have the following code
      """
      class User
      {
          /**
           * @return void
           */
          public function someMethod()
          {

          }
      }

      $user = Mockery::mock('NS\User[someMethod]', []);

      if (is_array($user)) {

      }
      """
    When I run Psalm
    Then I see these errors
      | Type                      | Message                                                                                                   |
      | DocblockTypeContradiction | / type Mockery\\MockInterface&NS\\User (does not contain array<[^,]+, mixed>\|for \$user is never array)/ |
    And I see no other errors

  Scenario: Alias class mocking is recognized
    Given I have the following code
      """
      class User
      {
      }

      $_user = Mockery::mock('alias:NS\User')->shouldReceive('someMethod');
      """
    When I run Psalm
    Then I see no errors

  Scenario: Overload class mocking is recognized
    Given I have the following code
      """
      class User
      {
      }

      $_user = Mockery::mock('overload:NS\User')->shouldReceive('someMethod');
      """
    When I run Psalm
    Then I see no errors

  Scenario: Expectations can be set on mocked instances
    Given I have the following code
      """
      class User
      {
          public function getName(): string {
            return 'name';
          }
      }
      $user = Mockery::mock(User::class);
      $user
        ->shouldReceive('getName')->andReturn('feek')
        ->shouldReceive('foo')->andReturnNull();
      """
    When I run Psalm
    Then I see no errors

  Scenario: Class spying is recognized
    Given I have the following code
      """
      class User
      {
      }
      function foo(User $user): User
      {
          return $user;
      }

      $_user = foo(Mockery::spy(User::class));
      """
    When I run Psalm
    Then I see no errors

  Scenario: New style stubbing
    Given I have the following code
      """
      class User
      {
        public function doThings(): int { return 10; }
      }

      function f(): int {
        $mock = Mockery::mock(User::class);
        $mock->allows()->doThings()->andReturns(1);
        return $mock->doThings();
      }
      """
    When I run Psalm
    Then I see no errors

  Scenario: New style mocking
    Given I have the following code
      """
      class User
      {
        public function doThings(): int { return 10; }
      }

      function f(): int {
        $mock = Mockery::mock(User::class);
        $mock->expects()->doThings()->andReturns(1);
        return $mock->doThings();
      }
      """
    When I run Psalm
    Then I see no errors
