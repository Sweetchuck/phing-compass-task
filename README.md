# phing-compass-task

This project is a [Phing](https://www.phing.info) build tool task for running
[Compass](http://compass-style.org/) commands.


``` xml
<target name="test">
    <taskdef name="compass-clean" classname="path-to.phing-compass.CompassCleanTask"/>
    <taskdef name="compass-compile" classname="path-to.phing-compass.CompassCompileTask"/>

    <compass-clean dir="my-theme-01"/>
    <compass-compile dir="my-theme-01" environment="production"/>
</target>
```
