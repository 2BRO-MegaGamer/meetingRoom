from pydub import AudioSegment
import os
files = os.listdir("volume_changer_{{$user_id}}/medias")
for x in range(len(files)):
    file_path = "volume_changer_{{$user_id}}/medias/"+files[x]
    file_name = files[x].split(".")
    song = AudioSegment.from_mp3(file_path)
    louder_song = song + int('{{$volume}}')
    louder_song.export(file_path, format=file_name[1])
    print(file_name)
