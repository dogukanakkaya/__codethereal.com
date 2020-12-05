@extends('admin.layouts.base')

@section('content')
    <x-breadcrumb :nav="$navigations"/>
    <div class="content">
        <div class="row">
            <div class="col-12 d-flex justify-content-between">
                <div class="nav nav-pills mb-3 border-active-tab" role="tablist">
                    <a class="nav-link active" data-toggle="pill" href="#general" role="tab"
                       aria-selected="true">{{ __('contents.general') }}</a>
                    <a class="nav-link" data-toggle="pill" href="#files" role="tab"
                       aria-selected="false">{{ __('contents.files') }}</a>
                </div>
            </div>
            <div class="col-12">
                <div class="list-area p-4">
                    {{ Form::open(['id' => 'content-form']) }}
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="nav nav-pills mb-3 language-tab" role="tablist">
                                @foreach($languages as $language)
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="pill"
                                       href="#general-{{ $language->code }}" role="tab"
                                       aria-selected="true">{{ strtoupper($language->code) }}</a>
                                @endforeach
                            </div>
                            <div class="tab-content">
                                @foreach($languages as $key => $language)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                         id="general-{{ $language->code }}" role="tabpanel">
                                        @include('admin.partials.description', ['text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, itaque!'])
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label("$language->code[title]", __('contents.title'), ['class' => 'required']) }}
                                                    {{ Form::text("$language->code[title]", '', ['class' => 'form-control']) }}
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label("$language->code[description]", __('contents.description')) }}
                                                    {{ Form::textarea("$language->code[description]", '', ['class' => 'form-control']) }}
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label("$language->code[full]", __('contents.full')) }}
                                                    {{ Form::textarea("$language->code[full]", '', ['class' => 'form-control rich-editor', 'rows' => 25]) }}
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        {{ Form::hidden("$language->code[active]", 0) }}
                                                        {{ Form::checkbox("$language->code[active]", 1, true, ['class' => 'custom-control-input', 'id' => "$language->code[active]"]) }}
                                                        {{ Form::label("$language->code[active]", __('contents.active'), ['class' => 'custom-control-label']) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        {{ Form::hidden("$language->code[searchable]", 0) }}
                                                        {{ Form::checkbox("$language->code[searchable]", 1, true, ['class' => 'custom-control-input', 'id' => "$language->code[searchable]"]) }}
                                                        {{ Form::label("$language->code[searchable]", __('contents.searchable'), ['class' => 'custom-control-label']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="row pt-3 border-top">
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label("content[parent_id]", __('contents.parent_content')) }}
                                            {{ Form::select("content[parent_id]", [0 => __('global.no')] + $parents, 0, ['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="files" role="tabpanel">
                            <div class="tab-content">
                                @include('admin.partials.description', ['text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, itaque!'])
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <x-dropzones.multiple index="1" input-name="files" folder="contents"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-right">
                        {{ Form::save() }}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
    <script>
        const form = document.getElementById('content-form')

        tinymce.init({
            selector: 'textarea.rich-editor',
            width: '100%',
            height: 500,
            statusbar: false,
            //language: "tr_TR",
            plugins: 'image code paste print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern code',
            paste_as_text: true,
            contextmenu: "link image imagetools table spellchecker ",
            toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect  | link | image  | removeformat ',
            images_upload_url: "{{ route('files.upload') }}",
            images_upload_handler: function (blobInfo, success, failure) {
                // TODO: convert to axios
                const xhr = new XMLHttpRequest()
                xhr.withCredentials = false
                xhr.open('POST', "{{ route('files.upload') }}")
                xhr.onload = function () {
                    const response = JSON.parse(xhr.responseText)
                    success(response.path)
                };
                const formData = new FormData()
                formData.append('folder', 'tinymce')
                formData.append('_token', '{{ csrf_token() }}')
                formData.append('file', blobInfo.blob(), blobInfo.filename())
                xhr.send(formData)
            },
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save()
                })
            }
        })

        const __onResponse = response => {
            makeToast(response.data)
            if (response.data.status) {
                form.reset()
            }
            toggleBtnLoading()
        }

        form.addEventListener('submit', e => {
            e.preventDefault()
            toggleBtnLoading()
            const formData = serialize(form, {hash: true})
            request.post('{{ route('contents.store') }}', formData)
                .then(__onResponse)
        })
    </script>
@endpush
