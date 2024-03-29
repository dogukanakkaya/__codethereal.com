@foreach($permissionGroups as $permissionGroup)
    <div class="row mb-3">
        @foreach($permissionGroup as $permission)
            <div class="col-md-3">
                <div class="basic-card-item-list">
                    <div class="head d-flex align-items-center justify-content-between p-3">
                        <div class="d-flex align-items-center">
                            <div class="char me-3">
                                <span>{{ substr($permission->title, 0, 1) }}</span>
                            </div>
                            <div>
                                <h3 class="mt-0">{{ $permission->title }}</h3>
                                <span class="text-white">{{ $permission->name }}</span>
                            </div>
                        </div>
                        <div class="actions">
                            <a href="javascript:void(0);"
                               onclick="__find({{ $permission->id }})"><i
                                    class="material-icons-outlined md-18">edit</i></a>
                            <a href="javascript:void(0);"
                               onclick="__delete({{ $permission->id }})"><i
                                    class="material-icons-outlined md-18">delete</i></a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endforeach
