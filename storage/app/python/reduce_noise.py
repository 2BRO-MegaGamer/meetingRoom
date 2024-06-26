from pydub import AudioSegment
import noisereduce as nr
import numpy as np
import os
files = os.listdir("reduce_noise_{{$user_id}}/medias")
for x in range(len(files)):
    file_path = "reduce_noise_{{$user_id}}/medias/"+files[x]
    file_name = files[x].split(".")
    audio = AudioSegment.from_file(file_path)
    samples = np.array(audio.get_array_of_samples())
    reduced_noise = nr.reduce_noise(samples, sr=audio.frame_rate)
    reduced_audio = AudioSegment(
        reduced_noise.tobytes(), 
        frame_rate=audio.frame_rate, 
        sample_width=audio.sample_width, 
        channels=audio.channels
    )
    reduced_audio.export(file_path, format=file_name[1])
    print(file_name)