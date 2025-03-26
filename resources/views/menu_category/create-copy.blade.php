<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            @if($auth_user->can('menu category list'))
                            <a href="{{ route('menu_category.index') }}" class="float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('menu_category.store'))
                        ->attribute('enctype', 'multipart/form-data')
                        ->attribute('data-toggle', 'validator')
                        ->id('menu-category-form')
                        ->open()
                    }}
                    {{ html()->hidden('id', $menuCategoryData->id ?? null) }}

                    @include('partials._language_toggale')

                    @foreach($language_array as $language)
                    <div id="form-language-{{ $language['id'] }}" class="language-form" style="display: {{ $language['id'] == app()->getLocale() ? 'block' : 'none' }};">
                        <div class="row">
                            @foreach(['name' => __('messages.name'), 'description' => __('messages.description')] as $field => $label)
                                <div class="form-group col-md-{{ $field === 'name' ? '4' : '12' }}">
                                    {{ html()->label($label . ($field === 'name' ? ' <span class="text-danger">*</span>' : ''), $field)->class('form-control-label language-label') }}
                                    
                                    @php
                                        $value = $language['id'] == 'en' 
                                            ? $menuCategoryData ? $menuCategoryData->$field : '' 
                                            : ($menuCategoryData ? $menuCategoryData->translate($field, $language['id']) : '');
                                        $name = $language['id'] == 'en' ? $field : "translations[{$language['id']}][$field]";
                                    @endphp

                                    @if($field === 'name')
                                        {{ html()->text($name, $value)
                                            ->placeholder($label)
                                            ->class('form-control')
                                            ->attribute('title', 'Please enter alphabetic characters and spaces only')
                                            ->attribute('data-required', 'true') }}
                                    @else
                                        {{ html()->textarea($name, $value)
                                            ->class('form-control textarea')
                                            ->rows(3)
                                            ->placeholder($label) }}
                                    @endif

                                    <small class="help-block with-errors text-danger"></small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                        <div class="form-group col-md-4">
                            <label class="form-control-label" for="menu_category_image">{{ __('messages.image') }} <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" name="menu_category_image" class="custom-file-input" id="menu_category_image"
                                 accept="image/*"  {{ is_null($menuCategoryData->id) ? 'required' : '' }}>
                                @if($menuCategoryData && getMediaFileExit($menuCategoryData, 'menu_category_image'))
                                    <label class="custom-file-label upload-label">{{ $menuCategoryData->getFirstMedia('menu_category_image')->file_name }}</label>
                                @else
                                    <label class="custom-file-label upload-label">{{ __('messages.choose_file',['file' =>  __('messages.image') ]) }}</label>
                                @endif
                            </div>
                            <small class="help-block with-errors text-danger" id="image-error"></small>
                        </div>

                        <img id="menu_category_image_preview" src="" width="150px" />

                        <div class="form-group col-md-4">
                            {{ html()->label(trans('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                            {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $menuCategoryData->status)->id('role')->class('form-control select2js')->required() }}
                        </div>

                        {{ html()->submit(trans('messages.save'))
                        ->class('btn btn-md btn-primary float-end')
                        ->id('saveButton') }}
                     {{ html()->form()->close() }}

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>
