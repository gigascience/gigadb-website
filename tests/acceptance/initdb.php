<?php

shell_exec("./protected/yiic migrate to 300000_000000 --connectionID=db --migrationPath=application.migrations.admin --interactive=0");
shell_exec("./protected/yiic migrate mark 000000_000000 --connectionID=db --interactive=0");
shell_exec("./protected/yiic migrate --connectionID=db --migrationPath=application.migrations.schema --interactive=0");
shell_exec("./protected/yiic migrate --connectionID=db --migrationPath=application.migrations.data.dev --interactive=0");
shell_exec("./protected/yiic sequencefixer fixAll");
shell_exec("./protected/yiic custommigrations refreshmaterializedviews");
shell_exec("./protected/yiic configchange searchresult --limit=2");