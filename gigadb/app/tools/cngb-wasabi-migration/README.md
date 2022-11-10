# CNGB Wasabi transfer tool

```
# Log into Docker container shell
$ docker-compose run --rm rclone sh
# Run test command
$ rclone ls wasabi:

# Execute on host
$ docker-compose run --rm rclone rclone ls wasabi:

# Run rclone script
$ docker-compose run --rm rclone /app/rclone_copy.sh --starting-doi 100002 --ending-doi 100304

$ docker-compose run --rm rclone /app/rclone_copy.sh --starting-doi 101200 --ending-doi 101960

$ docker-compose run --rm rclone /app/rclone_copy.sh --starting-doi 102100 --ending-doi 102990

# Delete directories during dev work
$ docker-compose run --rm rclone /app/rclone_reset.sh

```