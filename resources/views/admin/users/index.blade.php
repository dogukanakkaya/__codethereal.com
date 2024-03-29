@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center">
        <x-breadcrumb :nav="$navigations"/>
        <div class="page-actions">
            @can('delete_users')
                {{ Form::delete(['onclick' => '__deleteChecked()', 'class' => 'd-none delete-checked']) }}
            @endcan
            {{ Form::refresh(['onclick' => '__refresh()']) }}
            @can('create_users')
            {{ Form::addNew(['onclick' => '__create()']) }}
            @endcan
        </div>
    </div>
    <div class="list-area p-4">
        @include('admin.partials.description', ['text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, itaque!'])
        <x-datatable :url="route('users.datatable')" :columns="$columns"/>
    </div>

    @include('admin.users.form-modal')
@endsection

@push('scripts')
    <script>
        let updateId = 0;
        const form = document.getElementById('user-form')
        const modal = '#user-form-modal';

        const __onResponse = response => {
            makeToast(response.data)
            if (response.data.status) {
                closeModal(modal)
                form.reset()
                __refresh()
            }
            toggleBtnLoading()
        }

        form.addEventListener('submit', e => {
            e.preventDefault()
            toggleBtnLoading()
            const formData = serialize(form, {hash: true})
            if (updateId > 0) {
                const url = '{{ route('users.update', ['id' => ':id']) }}'.replace(':id', updateId)
                request.put(url, formData)
                    .then(__onResponse)
            } else {
                request.post('{{ route('users.create') }}', formData)
                    .then(__onResponse)
            }
        })

        const __create = () => {
            if (updateId > 0) {
                form.reset()
                updateId = 0;
                clearPreviewFull1()
            }
            getPermissions().then(response => {
                document.getElementById('user-permissions').innerHTML = response.data
                //changeModalTitle(modal, '{{ __('global.add_new') }}')
                openModal(modal)
                changeModalTitle(modal, '{{ __('users.add_new') }}')
            })
        }

        const __delete = id => {
            if (confirm('{{ __('messages.confirmation.delete.custom_message') }}')) {
                const url = '{{ route('users.destroy', ['id' => ':id']) }}'.replace(':id', id)
                request.delete(url)
                    .then(res => {
                        res.data.addition = `<a href="javascript:void(0);" onclick="__undoDelete(${id})">{{ __('buttons.undo') }}</a>`;
                        __onResponse(res)
                    })
            }
        }

        const __undoDelete = id => {
            const url = '{{ route('users.restore', ['id' => ':id']) }}'.replace(':id', id)
            request.get(url)
                .then(__onResponse)
        }

        const __find = id => {
            updateId = id
            const url = '{{ route('users.find', ['id' => ':id']) }}'.replace(':id', id)
            getPermissions().then(response => {
                document.getElementById('user-permissions').innerHTML = response.data

                request.get(url)
                    .then(response => {
                        document.querySelector('input[name=name]').value = response.data.name
                        document.querySelector('input[name=email]').value = response.data.email
                        document.querySelector('input[name=position]').value = response.data.position
                        document.querySelector('textarea[name=about]').value = response.data.about
                        document.querySelector('input[name=image]').value = response.data.image
                        if (response.data.image > 0) {
                            // x-dropzone component default index is 1, so we call createPreview1()
                            createPreview1(response.data.image, storage(response.data.path))
                        }

                        response.data.permissions.forEach(permission => {
                            document.querySelector(`input[type=checkbox][value=${permission.name}]`).setAttribute('checked', true)
                        })
                        changeModalTitle(modal, '{{ __('users.update', ['title' => ':title']) }}'.replace(':title', response.data.name))
                        openModal(modal)
                    })
            })

        }

        const getPermissions = () => request.get('{{ route('permissions.checkboxes') }}')
    </script>
@endpush
