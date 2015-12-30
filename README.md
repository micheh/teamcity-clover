Clover.xml parser for TeamCity
==============================

This small php script integrates the code coverage information from PHPUnit into TeamCity.

Service messages are used to notify TeamCity of the build metrics, since usage of `teamcity-info.xml` has been deprecated, and support may be removed in future versions of TeamCity.  Documentation: https://confluence.jetbrains.com/display/TCD9/Build+Script+Interaction+with+TeamCity#BuildScriptInteractionwithTeamCity-teamcity-info.xml

The metrics will appear in the TeamCity GUI by default, under the Statistics tab for your build configuration.  As of TeamCity 9, you can easily add custom charts if your reporting needs are more sophisticated.  Documentation: https://confluence.jetbrains.com/display/TCD9/Customizing+Statistics+Charts

Additionally, these metrics can be used to enforce standards on builds.  For example, you can update your build configuration to enforce that code coverage has not dropped below some pre-determined threshold, or has not dropped by some percentage.  Documentation: https://confluence.jetbrains.com/display/TCD9/Build+Failure+Conditions#BuildFailureConditions-fail-metric-change



Installation
------------
Use composer to add teamcity-clover to your project: `composer require --dev micheh/teamcity-clover`.

Or manually add it to your `composer.json` file with:

```json
"require-dev": {
    "micheh/teamcity-clover": "^0.1.0"
}
```

To use the script without composer, [download the current version](https://github.com/micheh/teamcity-clover/archive/master.zip) and extract the files to a location where TeamCity has access.


Usage
-----
Run PHPUnit and make sure to use the `--coverage-clover` argument to create the clover.xml. Then add
another build step to run the `teamcity-clover.php` script and provide the path to the clover.xml as
the only argument, for example: `php path/to/teamcity-clover.php %system.teamcity.build.tempDir%/clover.xml`.

If you are using the PHPUnit Meta-Runner from the Jetbrains Meta-Runner Power Pack, the above path is where you'll find clover.xml.

Mapping
-------
Since TeamCity and PHPUnit do not provide the same code coverage information, the different attributes
had to be matched. The code coverage mapping is as follows:

TeamCity | PHPUnit        | Statistic Key Names
-------- | -------------- | -------------------------------------------------------------
Lines    | Elements       | CodeCoverageAbsLTotal, CodeCoverageAbsLCovered, CodeCoverageL
Blocks   | Statements     | CodeCoverageAbsBTotal, CodeCoverageAbsBCovered, CodeCoverageB
Methods  | Methods        | CodeCoverageAbsMTotal, CodeCoverageAbsMCovered, CodeCoverageM
Classes  | Classes/Traits | CodeCoverageAbsCTotal, CodeCoverageAbsCCovered, CodeCoverageC


Custom statistics
-----------------
In addition to the code coverage, the following custom statistic values are reported to TeamCity:

Custom Statistic Key  | Description
--------------------- | -----------------------------------
Files                 | Amount of files
LinesOfCode           | Amount of lines of code
NonCommentLinesOfCode | Amount of non-comment lines of code


Custom Graphs
-------------
Besides the automatically created code coverage graphs, you can create graphs for the custom statistics
as well, by updating the `<TeamCity Data Directory>/config/main-config.xml`. For example:

```xml
<graph title="Metrics" defaultFilters="showFailed" seriesTitle="Type">
    <valueType key="Files" title="Files" />
    <valueType key="CodeCoverageAbsCTotal" title="Classes" />
    <valueType key="CodeCoverageAbsMTotal" title="Methods" />
</graph>
<graph title="Lines" defaultFilters="showFailed" seriesTitle="Type">
    <valueType key="LinesOfCode" title="Lines of code" />
    <valueType key="NonCommentLinesOfCode" title="Non-Comment lines of code" />
</graph>
```

See the [TeamCity documentation](http://confluence.jetbrains.com/display/TCD8/Custom+Chart) for more information.

Since TeamCity 9.x, you can also add custom graphs via the TeamCity GUI, instead of manually editing project XML's.  See TeamCity documentation for details.

License
-------
The files in this archive are licensed under the BSD-3-Clause license.
You can find a copy of this license in [LICENSE.txt](LICENSE.txt).
