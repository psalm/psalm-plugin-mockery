Feature: PHPUnitIntegration

  Background:
    Given I have the following config
      """
      <?xml version="1.0"?>
      <psalm totallyTyped="true">
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
      use PHPUnit\Framework\TestCase;

      """

  Scenario: Mockery trait does not cause Psalm to throw fatal error
    Given I have the following code
      """
      /** @psalm-suppress PropertyNotSetInConstructor */
      class MyTestCase extends TestCase
      {
          use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

          /**
           * @return void
           */
          public function testSomething()
          {

          }
      }
      """
    And I have the "mockery/mockery" package satisfying the "^1.0"
    When I run Psalm
    Then I see no errors

  Scenario: Plugin stubs don't conflict with Mockery 0.9.x
      Given I have the following code
        """
        /** @psalm-suppress PropertyNotSetInConstructor */
        class MyTestCase extends TestCase
        {
            /**
             * @return void
             */
            public function testSomething()
            {
                $mock = Mockery::mock(\ArrayAccess::class);
            }
        }
        """
      And I have the "mockery/mockery" package satisfying the "< 1.0"
      When I run Psalm
      Then I see no errors
