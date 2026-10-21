@availability @availability_stash
Feature: Activity access can be based on Stash items

  Scenario: Collecting an item unlocks an activity
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | c1        |
    And the following "users" exist:
      | username |
      | s1       |
    And the following "course enrolments" exist:
      | user | course | role    |
      | s1   | c1     | student |
    And the following "blocks" exist:
      | blockname | contextlevel | reference | configdata |
      | stash     | Course       | c1        |            |
    And the following "activities" exist:
      | activity | course | name         | idnumber | content             |
      | page     | c1     | Bonus lesson | BONUS    | The treasure awaits |
    And the following "availability_stash > items" exist:
      | course | name |
      | c1     | Key  |
    And the following "availability_stash > restrictions" exist:
      | activity | item | condition | quantity |
      | BONUS    | Key  | ==        | 1        |
    And I am on the "c1" "course" page logged in as "s1"
    And I should not see "Bonus lesson"
    And the following "availability_stash > user items" exist:
      | user | item | quantity |
      | s1   | Key  | 1        |
    When I am on the "c1" "course" page
    Then I should see "Bonus lesson"
    And I follow "Bonus lesson"
    And I should see "The treasure awaits"
