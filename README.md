Clover.xml parser for TeamCity
==============================

This small php script integrates the code coverage information from PHPUnit into TeamCity.
It creates the `teamcity-info.xml` (or append the metrics to an already existing xml file),
which will automatically be parsed by TeamCity and the metrics will appear in the GUI.

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


License
-------
The files in this archive are licensed under the BSD-3-Clause license.
You can find a copy of this license in [LICENSE.txt](LICENSE.txt).
