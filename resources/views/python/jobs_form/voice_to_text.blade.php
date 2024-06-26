@extends('layouts.layout')
<?php
$title="Voice to Speach Job";
?>
@section('content')
<div class="container mt-3">
    <div class="card bg-dark text-light rounded p-3 border border-1 border-light">
        <div class="card-header">
            <div class="w-100 text-center">Enter the audio file you want to convert to text here</div>
        </div>
        <div class="card-body w-100 text-center text-light">
            <button class="btn btn-outline-info " data-bs-toggle="modal" data-bs-target="#upload_file_modal">Select file</button>
            <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="upload_file_modal">
                <div class="modal-dialog">
                    <div class="modal-content bg-dark">
                        <div class="modal-title">
                            <button type="button" class="btn-close d-none" data-bs-dismiss="modal" aria-label="Close" id="progress_close_btn"></button>
                            <div class="my-3 w-100">
                                <button class="btn btn-outline-light " id="upload_file_btn">upload File</button>
                            </div>
                            <div class="w-100 p-0 m-0 mb-3">
                                <select class="form-select" name="language_select" id="language_select">
                                    <option class="bg-dark text-center text-light" value="" selected>Select language</option>
                                    <option class="bg-dark text-center text-light" value="'fa-IR'">fa-IR</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-body overflow-y-auto" style="max-height: 30vh;" id="show_file_uploaded"></div>
                        <div class="progress rounded-0 mt-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar rounded-0 progress-bar-striped progress-bar-animated" id="upload_file_progress" style="width: 0%"></div>
                        </div>
                        <hr class="mb-0">
                        <div class="w-100 p-0 m-0">
                            <button class="btn w-100 btn-success rounded-top-0" id="to_text_btn" disabled>To text</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer position-relative text-center fw-bold d-none" id="card_footer">
            <button class="position-absolute start-0 top-0 translate-middle-x rounded-0 border-0 btn text-light" id="copy_result"><i class="bi bi-clipboard"></i></button>
            <h6 class="overflow-y-auto" id="show_result" style="max-height: 40vh"></h6>
        </div>
    </div>
</div>
@vite(['resources/js/python/voice_to_speach_form.js'])

@endsection
