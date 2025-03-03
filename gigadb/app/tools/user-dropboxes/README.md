# How to create user dropboxes  for the curator

1. Connect with SSH to the files server for the live production environment
2. Execute `history` to see what set of drop-boxes have recently been created
```shell
history | grep "make_dropbox"
```
wil show something like:
```shell
114  ./make_dropbox.sh user292 user293 user294 user295 user296 user297 user298 user299 user300 user301 user302 user303 user304 user305 user306 user307 user308 user309 user310 user311
116  ./make_dropbox.sh user312 user313 user314 user315 user316 user317 user318 user319 user320 user321 user322 user323 user324 user325 user326 user327 user328 user329 user330 user331
118  ./make_dropbox.sh user332 user333 user334 user335 user336 user337 user338 user339 user340 user341 user342 user343 user344 user345 user346 user347 user348 user349 user350 user351
120  cat ./make_dropbox.sh
130  more make_dropbox.sh
139  history | grep "make_dropbox"
```

You can also open the log file `new_dropboxes.txt` in the home directoy to see a time-stamped audit of the creation of dropbox credentials
```shell
cat new_dropboxes.txt
```

4. Create the next set of user drop-boxes

I tend to create them in a batch of 20.

```shell
./make_dropbox.sh userXYZ userXYZ userXYZ ...
```

>**Important:** Make sure to keep each drop-box's name unique. 
> If there is a duplicate, the script will throw an error saying the account already exist
> If that happen, you can correct the name and rerun the command for the correct name and the rest of the batch not yet created.
> (if you rerunthe command for the full batch if twill throw errors for the ones already corrected).

Here's an example of session from `history` that illustrating the mistakes and its correction:
```shell
104  ./make_dropbox.sh user272 user273 user274 user275 user276 user257 user257 user279 user280 user281 user282 user283 user284 user285 user286 user287 user288 user289 user290 user291
  105  cat new_dropboxes.txt
  106  ./make_dropbox.sh user272 user273 user274 user275 user276 user277 user278 user279 user280 user281 user282 user283 user284 user285 user286 user287 user288 user289 user290 user291
  107  cat new_dropboxes.txt
  108  ./make_dropbox.sh user277 user278 user279 user280 user281 user282 user283 user284 user285 user286 user287 user288 user289 user290 user291
  109  cat new_dropboxes.txt
```

5. Informing the curators 

Every time the command is run, it will append to `new_dropboxes.txt` a timestamp and the ftp credentials for all the user dropboxes passed in arguments and succesfully created.
You need to email those credentials to the curators.