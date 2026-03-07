@extends('layouts.admin')

@section('title', __('admin.template_settings'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.template_settings') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.template_settings') }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="list-group">
                <a href="{{ route('admin.pengaturan.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-gear me-2"></i>{{ __('admin.general_settings') }}
                </a>
                <a href="{{ route('admin.pengaturan.index', ['tab' => 'contact']) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-telephone me-2"></i>{{ __('admin.contact_info') }}
                </a>
                <a href="{{ route('admin.pengaturan.index', ['tab' => 'social']) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-share me-2"></i>{{ __('admin.social_media') }}
                </a>
                <a href="{{ route('admin.pengaturan.template') }}" class="list-group-item list-group-item-action active">
                    <i class="bi bi-palette me-2"></i>{{ __('admin.template_settings') }}
                </a>
            </div>
        </div>

        <div class="col-lg-9">
            <form action="{{ route('admin.pengaturan.template.update') }}" method="POST">
                @csrf

                <div class="card mb-4">
                    <div class="card-header">{{ __('admin.color_scheme') }}</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">{{ __('admin.primary_color') }}</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="primary_color_picker" value="{{ $settings['primary_color'] ?? '#0d6efd' }}" onchange="document.getElementById('primary_color').value = this.value">
                                        <input type="text" class="form-control" id="primary_color" name="settings[primary_color]" value="{{ $settings['primary_color'] ?? '#0d6efd' }}" onchange="document.getElementById('primary_color_picker').value = this.value">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="secondary_color" class="form-label">{{ __('admin.secondary_color') }}</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="secondary_color_picker" value="{{ $settings['secondary_color'] ?? '#6c757d' }}" onchange="document.getElementById('secondary_color').value = this.value">
                                        <input type="text" class="form-control" id="secondary_color" name="settings[secondary_color]" value="{{ $settings['secondary_color'] ?? '#6c757d' }}" onchange="document.getElementById('secondary_color_picker').value = this.value">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="header_bg_color" class="form-label">{{ __('admin.header_bg_color') }}</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="header_bg_color_picker" value="{{ $settings['header_bg_color'] ?? '#ffffff' }}" onchange="document.getElementById('header_bg_color').value = this.value">
                                        <input type="text" class="form-control" id="header_bg_color" name="settings[header_bg_color]" value="{{ $settings['header_bg_color'] ?? '#ffffff' }}" onchange="document.getElementById('header_bg_color_picker').value = this.value">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="footer_bg_color" class="form-label">{{ __('admin.footer_bg_color') }}</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="footer_bg_color_picker" value="{{ $settings['footer_bg_color'] ?? '#212529' }}" onchange="document.getElementById('footer_bg_color').value = this.value">
                                        <input type="text" class="form-control" id="footer_bg_color" name="settings[footer_bg_color]" value="{{ $settings['footer_bg_color'] ?? '#212529' }}" onchange="document.getElementById('footer_bg_color_picker').value = this.value">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">{{ __('admin.layout_options') }}</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="navbar_style" class="form-label">{{ __('admin.navbar_style') }}</label>
                                    <select class="form-select" id="navbar_style" name="settings[navbar_style]">
                                        <option value="light" {{ ($settings['navbar_style'] ?? 'light') == 'light' ? 'selected' : '' }}>Light</option>
                                        <option value="dark" {{ ($settings['navbar_style'] ?? '') == 'dark' ? 'selected' : '' }}>Dark</option>
                                        <option value="primary" {{ ($settings['navbar_style'] ?? '') == 'primary' ? 'selected' : '' }}>Primary Color</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="container_width" class="form-label">{{ __('admin.container_width') }}</label>
                                    <select class="form-select" id="container_width" name="settings[container_width]">
                                        <option value="container" {{ ($settings['container_width'] ?? 'container') == 'container' ? 'selected' : '' }}>Default (1140px)</option>
                                        <option value="container-lg" {{ ($settings['container_width'] ?? '') == 'container-lg' ? 'selected' : '' }}>Large (960px)</option>
                                        <option value="container-xl" {{ ($settings['container_width'] ?? '') == 'container-xl' ? 'selected' : '' }}>Extra Large (1200px)</option>
                                        <option value="container-xxl" {{ ($settings['container_width'] ?? '') == 'container-xxl' ? 'selected' : '' }}>XXL (1400px)</option>
                                        <option value="container-fluid" {{ ($settings['container_width'] ?? '') == 'container-fluid' ? 'selected' : '' }}>Full Width</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_breadcrumb" name="settings[show_breadcrumb]" value="1" {{ ($settings['show_breadcrumb'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_breadcrumb">{{ __('admin.show_breadcrumb') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_back_to_top" name="settings[show_back_to_top]" value="1" {{ ($settings['show_back_to_top'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_back_to_top">{{ __('admin.show_back_to_top') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">{{ __('admin.homepage_sections') }}</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_slider" name="settings[show_slider]" value="1" {{ ($settings['show_slider'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_slider">{{ __('admin.show_slider') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_welcome" name="settings[show_welcome]" value="1" {{ ($settings['show_welcome'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_welcome">{{ __('admin.show_welcome') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_news" name="settings[show_news]" value="1" {{ ($settings['show_news'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_news">{{ __('admin.show_news') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_announcement" name="settings[show_announcement]" value="1" {{ ($settings['show_announcement'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_announcement">{{ __('admin.show_announcement') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_agenda" name="settings[show_agenda]" value="1" {{ ($settings['show_agenda'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_agenda">{{ __('admin.show_agenda') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_gallery" name="settings[show_gallery]" value="1" {{ ($settings['show_gallery'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_gallery">{{ __('admin.show_gallery') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>{{ __('admin.save_settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
