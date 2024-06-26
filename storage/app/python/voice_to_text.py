import speech_recognition as sr
import os
files = os.listdir("voice_to_text_{{$user_id}}/medias")
r = sr.Recognizer()
note_pad = open(("voice_to_text_{{$user_id}}/result.txt"),"w+",encoding="utf-8")
result = []
language_input="{{$language}}"
language_input = language_input.replace("'","")
for x in range(len(files)):
    file_path = "voice_to_text_{{$user_id}}/medias/"+files[x]
    file_name = files[x].split(".")
    audio_file = sr.AudioFile(file_path)
    with audio_file as source:
        audio=r.record(source)
    try:
        text = r.recognize_google(audio,language=language_input)
        result.append([file_name[0],text])
    except:
        pass
for y in result:
    note_pad.write("\r\n\r\n"+y[0]+"\r\n" + y[1])
print("done")