Feature: MockReturn
  
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
      
      """
    
  Scenario: Defined method mocking sets proper intersection return type (Psalm 3.0.9 and higher)
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
    And I have Psalm newer than "3.0.8" (because of "error messages changing in 3.0.9")
    When I run Psalm
    Then I see these errors
      | Type            | Message                                                                                                                                 |
      | DocblockTypeContradiction | Cannot resolve types for $user - docblock-defined type Mockery\MockInterface&NS\User does not contain array<array-key, mixed> |
    And I see no other errors
    
  Scenario: Defined method mocking sets proper intersection return type (Psalm 3.0.8 and lower)
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
    And I have Psalm older than "3.0.9" (because of "error messages changing in 3.0.9")
    When I run Psalm
    Then I see these errors
      | Type            | Message                                                                                                                                 |
      | DocblockTypeContradiction | Cannot resolve types for $user - docblock-defined type Mockery\MockInterface&NS\User does not contain array<mixed, mixed> |
    And I see no other errors
    