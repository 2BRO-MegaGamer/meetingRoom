>## ***<p align="center">MeetingRoom</p>***
<p align="center">This is a simple project to use peerjs.</p>

## <p align="center">Setup</p> 
```
composer install
npm install
```
Then make a copy of the **.env.example** file and then rename it to **.env**

**if you want to use Python**
>[Download FFmpeg](https://www.ffmpeg.org/download.html)

And put it in the following path
```
/storage/app/python/inaction
```


You can modify the .env file if you want
```
php artisan key:generate
php artisan migrate
npm run build
```
## <p align="center">Run the code</p>
Open Tow terminals. One command for each
```
php artisan serve
peerjs --port 9000
```
