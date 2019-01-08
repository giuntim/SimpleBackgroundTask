# SimpleBackgroundTask
A simple class to run background tasks in PHP

## Example of use:

```
$task=new SimpleBackgroundTask;
$task->run('ls /etc');
while($task->isRunning()) echo "wait until completes\n";
echo $task->getOutput();
unset($task);
```

> NOTE: as far as I know, this class works only on Unix-like systems.

