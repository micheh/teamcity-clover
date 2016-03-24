Clover.xml parser for TeamCity
==============================

This small php script integrates the code coverage information from PHPUnit into TeamCity. The script uses 
[service messages](https://confluence.jetbrains.com/display/TCD9/Build+Script+Interaction+with+TeamCity) to notify TeamCity of the build metrics 
(since usage of `teamcity-info.xml` has been deprecated, and support may be removed in future versions of TeamCity).

The metrics will appear in the TeamCity GUI by default (under the Overview tab for a specific build and under the Statistics tab for your build configuration).
As of TeamCity 9, you can easily add custom charts if your reporting needs are more sophisticated (see below).

Additionally, these metrics can be used to enforce standards on builds. For example, you can update your
build configuration to enforce that code coverage has not dropped below some pre-determined threshold, 
or has not dropped by some percentage. See the [TeamCity documentation](https://confluence.jetbrains.com/display/TCD9/Build+Failure+Conditions#BuildFailureConditions-fail-metric-change) for more information.


Installation
------------
Use composer to add teamcity-clover to your project: `composer require --dev micheh/teamcity-clover`.

Or manually add it to your `composer.json` file with:

```json
"require-dev": {
    "micheh/teamcity-clover": "~0.6"
}
```

To use the script without composer, [download the current version](https://github.com/micheh/teamcity-clover/archive/master.zip) and extract the files to a location where TeamCity has access.


Usage
-----
Run PHPUnit and make sure to use the `--coverage-clover` argument to create the clover.xml. Then add
another build step to run the `teamcity-clover.php` script and provide the path to the clover.xml as
the only argument, for example: `php path/to/teamcity-clover.php %system.teamcity.build.tempDir%/clover.xml`.
If you are using the PHPUnit Meta-Runner from the [JetBrains Meta-Runner Power Pack](https://github.com/JetBrains/meta-runner-power-pack/tree/master/php), the above path is where you'll find the clover.xml.

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
In addition to the code coverage, the script will also report custom statistic values to TeamCity. 
These values do not appear in the web interface by default, but can be used for custom graphs or for build failure conditions.

### CRAP Index ###
PHPUnit calculates a Change Risk Anti-Patterns (CRAP) Index for each method, which depends on the 
cyclomatic complexity and code coverage (see [this question on StackOverflow](https://stackoverflow.com/q/4731774) for more information).
The script will report the total, average, and maximum CRAP index to TeamCity, as well as the number and 
percentage of methods with a CRAP index equal or above a specified threshold (by default 30). You can 
change the CRAP threshold by providing the `--crap-threshold <number>` argument to the script. For example,
to count all methods with a CRAP index of 20 or more, use `php teamcity-clover.php --crap-threshold 20 clover.xml`.
Set the threshold to 0 to disable the reporting of the CRAP metrics.

Custom Statistic Key  | Description
--------------------- | -----------------------------------------------------------------
CRAPAmount            | Number of methods with a CRAP index >= threshold (default: 30)
CRAPPercent           | Percentage of methods with a CRAP index >= threshold (default: 30)
CRAPTotal             | Total CRAP index (sum of all CRAP indices)
CRAPAverage           | Average CRAP index over all methods
CRAPMaximum           | Highest CRAP index reported


### Other metrics ###
In addition, the following various metrics are reported to TeamCity:

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

If you use TeamCity 9 or newer, you can also add custom graphs directly via the TeamCity web interface, instead of manually editing project XML's.
See the [TeamCity documentation](https://confluence.jetbrains.com/display/TCD9/Custom+Chart) for more information.


License
-------
The files in this archive are licensed under the BSD-3-Clause license.
You can find a copy of this license in [LICENSE.md](LICENSE.md).
