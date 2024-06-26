@extends('layouts.layout')
<?php
$title="Convert files job";
?>
@section('content')

<div class="container mt-3">
    <div class="card bg-dark text-light rounded p-3 border border-1 border-light">
        <div class="card-header">
            <div class="w-100 text-center">Enter the  file you want to convert </div>
        </div>
        <div class="card-body w-100 text-center text-light">
            <button class="btn btn-outline-info " data-bs-toggle="modal" data-bs-target="#upload_file_modal" id="upload_file_modal_btn">Select file</button>
            <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="upload_file_modal">
                <div class="modal-dialog">
                    <div class="modal-content bg-dark">
                        <div class="modal-title">
                            <button type="button" class="btn-close d-none" data-bs-dismiss="modal" aria-label="Close" id="progress_close_btn"></button>
                            <div class="my-3 w-100">
                                <button class="btn btn-outline-light " id="upload_file_btn">upload File</button>
                            </div>
                            <hr>
                            <div class="w-100 p-0 m-0">
                                <div class="w-100 text-center">From:</div>
                                <div class="w-100 text-center fw-bold" id="type_of_your_file">select your file first</div>
                                <hr>
                                <div class="w-100 text-center">To:</div>
                                <div class="w-100 text-center fw-bold d-none" id="type_of_convert_file"></div>
                                <button class="btn btn-secondary d-none" id="btn_select_to" disabled>Select</button>
                            </div>
                        </div>
                        <div class="modal-body overflow-y-auto" style="max-height: 30vh;" id="show_file_uploaded"></div>
                        <div class="progress rounded-0 mt-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar rounded-0 progress-bar-striped progress-bar-animated" id="upload_file_progress" style="width: 0%"></div>
                        </div>
                        <hr class="mb-0">
                        <div class="w-100 p-0 m-0">
                            <button class="btn w-100 btn-success rounded-top-0" id="convert_btn" disabled>Convert to</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer position-relative p-0 m-0 text-center fw-bold" id="card_footer">
            <div class="overflow-y-auto p-0 m-0" id="show_result" style="max-height: 40vh">
                {{-- <div class="w-100 row mb-3 rounded border border-1 border-light p-0 m-0">
                    <div class="col m-auto">
                        filename
                    </div>
                    <div class="col">
                        <button class="btn btn-outline-success">download</button>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
    <div class="w-0 h-0">
        <button class="d-none" id="type_select_modal_btn" data-bs-toggle="modal" data-bs-target="#type_select_modal"></button>
        <div class="modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true" id="type_select_modal">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-light">
                    <div class="modal-header">
                        <div class="modal-title text-center">Select Type :</div>
                        <button type="button" class="btn-close d-none" id="close_select_type_modal_btn" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 m-0" id="select_type_modal_body">
                        <div class="w-100">
                            <div class="input-group my-3">
                                <input type="text" class="form-control rounded-0 bg-dark text-light text-center border-1" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
                                <button class="btn btn-outline-danger rounded-0 border border-danger border-start-0" type="button" id="clear_search_input"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>
                        <hr>
                        <div class="w-100 " style="max-height: 40vh">
                            <div class="w-100 h-100 p-0 m-0">
                                <div class="accordion bg-dark" id="accordion_select_type">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.accordion-button::after {
    background-image: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'><path fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/></svg>") !important;
    rotate: 90deg;
}

.accordion-button:not(.collapsed)::after {
    background-image: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'><path fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/></svg>") !important;
    rotate: 90deg;
}
</style>
@vite(['resources/js/python/convert_file.js'])

@endsection








{{-- 
<div class="modal-body p-0 m-0" id="select_type_modal_body">
    <div class="w-100">
        <div class="input-group my-3">
            <input type="text" class="form-control rounded-0 bg-dark text-light text-center border-1" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
            <button class="btn btn-outline-danger rounded-0 border border-danger border-start-0" type="button" id="clear_search_input"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>
    <hr>
    <div class="w-100 h-100 p-0 m-0 row" >
        <div class="col row row-cols-1" style="max-width: fit-content" id="type_show_btns">
            <button class="btn col p-0 mb-3 text-light border-bottom position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#multiCollapseExample1" aria-expanded="false" aria-controls="multiCollapseExample2">audio <div class="position-absolute start-100 top-50 translate-middle-y"><i class="bi bi-caret-right"></i></div></button>
            <button class="btn col p-0 mb-3 text-light border-bottom position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#multiCollapseExample2" aria-expanded="false" aria-controls="multiCollapseExample1">video<div class="position-absolute start-100 top-50 translate-middle-y"><i class="bi bi-caret-right"></i></div></button>
        </div>
        <div class="w-50 col" id="all_type_selection">
            <div class="w-100 collapse multi-collapse" id="multiCollapseExample1">
                <div class="w-100 p-0 m-0 row">
                    <div class="col p-0 m-0">
                        <div class="p-1 badge fw-bold">
                            <button class="w-100 h-100 m-0 btn btn-outline-light text-uppercase">test</button>
                        </div>
                    </div> 
                </div>
                <hr>
            </div>
        </div>
    </div>
</div> --}}
