Feature:
  To verify that core bundles are loaded
  As an Engineer
  I would like to check for existence of loaded bundles

  Scenario: Core Kernel has loaded bundles
    When an instance of the Kernel class is loaded
    And the "registerBundles" method is called
    Then the result should be positive