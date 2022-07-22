<?php

shell_exec("./yii_test migrate/down all --interactive=0");
shell_exec("./yii_test migrate/up --interactive=0");