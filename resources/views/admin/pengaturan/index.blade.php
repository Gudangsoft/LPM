@extends('layouts.admin')

@section('title', __('admin.settings'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.settings') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.settings') }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="list-group">
                <a href="{{ route('admin.pengaturan.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.pengaturan.index') && !request('tab') ? 'active' : '' }}">
                    <i class="bi bi-gear me-2"></i>{{ __('admin.general_settings') }}
                </a>
                <a href="{{ route('admin.pengaturan.index', ['tab' => 'contact']) }}" class="list-group-item list-group-item-action {{ request('tab') == 'contact' ? 'active' : '' }}">
                    <i class="bi bi-telephone me-2"></i>{{ __('admin.contact_info') }}
                </a>
                <a href="{{ route('admin.pengaturan.index', ['tab' => 'social']) }}" class="list-group-item list-group-item-action {{ request('tab') == 'social' ? 'active' : '' }}">
                    <i class="bi bi-share me-2"></i>{{ __('admin.social_media') }}
                </a>
                <a href="{{ route('admin.pengaturan.template') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.pengaturan.template') ? 'active' : '' }}">
                    <i class="bi bi-palette me-2"></i>{{ __('admin.template_settings') }}
                </a>
            </div>
        </div>

        <div class="col-lg-9">
            <form action="{{ route('admin.pengaturan.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if(!request('tab') || request('tab') == 'general')
                <div class="card mb-4">
                    <div class="card-header">{{ __('admin.general_settings') }}</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="site_name" class="form-label">{{ __('admin.site_name') }}</label>
                            <input type="text" class="form-control" id="site_name" name="settings[site_name]" value="{{ $settings['site_name'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label for="site_tagline" class="form-label">{{ __('admin.tagline') }}</label>
                            <input type="text" class="form-control" id="site_tagline" name="settings[site_tagline]" value="{{ $settings['site_tagline'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label for="site_description" class="form-label">{{ __('admin.description') }}</label>
                            <textarea class="form-control" id="site_description" name="settings[site_description]" rows="3">{{ $settings['site_description'] ?? '' }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.logo') }}</label>
                                    @if(isset($settings['site_logo']) && $settings['site_logo'])
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($settings['site_logo']) }}" alt="" style="max-height: 50px;">
                                    </div>
                                    @endif
                                    <input type="file" class="form-control" name="site_logo" accept="image/*">
                                    <small class="text-muted">{{ __('admin.recommended_size') }}: 200x50px</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.favicon') }}</label>
                                    @if(isset($settings['site_favicon']) && $settings['site_favicon'])
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($settings['site_favicon']) }}" alt="" style="max-height: 32px;">
                                    </div>
                                    @endif
                                    <input type="file" class="form-control" name="site_favicon" accept="image/x-icon,image/png">
                                    <small class="text-muted">{{ __('admin.recommended_size') }}: 32x32px</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="footer_text" class="form-label">{{ __('admin.footer_text') }}</label>
                            <textarea class="form-control" id="footer_text" name="settings[footer_text]" rows="2">{{ $settings['footer_text'] ?? '' }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="current_period" class="form-label">{{ __('admin.current_period') }}</label>
                            <input type="text" class="form-control" id="current_period" name="settings[current_period]" value="{{ $settings['current_period'] ?? '' }}">
                            <small class="text-muted">{{ __('admin.current_period_help') }}</small>
                        </div>
                    </div>
                </div>
                @endif

                @if(request('tab') == 'contact')
                <div class="card mb-4">
                    <div class="card-header">{{ __('admin.contact_info') }}</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="contact_address" class="form-label">{{ __('admin.address') }}</label>
                            <textarea class="form-control" id="contact_address" name="settings[contact_address]" rows="3">{{ $settings['contact_address'] ?? '' }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_phone" class="form-label">{{ __('admin.phone') }}</label>
                                    <input type="text" class="form-control" id="contact_phone" name="settings[contact_phone]" value="{{ $settings['contact_phone'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_fax" class="form-label">Fax</label>
                                    <input type="text" class="form-control" id="contact_fax" name="settings[contact_fax]" value="{{ $settings['contact_fax'] ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_email" class="form-label">{{ __('admin.email') }}</label>
                                    <input type="email" class="form-control" id="contact_email" name="settings[contact_email]" value="{{ $settings['contact_email'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_whatsapp" class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control" id="contact_whatsapp" name="settings[contact_whatsapp]" value="{{ $settings['contact_whatsapp'] ?? '' }}" placeholder="628xxxxxxxxxx">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contact_maps" class="form-label">Google Maps Embed URL</label>
                            <input type="url" class="form-control" id="contact_maps" name="settings[contact_maps]" value="{{ $settings['contact_maps'] ?? '' }}" placeholder="https://www.google.com/maps/embed?...">
                        </div>
                    </div>
                </div>
                @endif

                @if(request('tab') == 'social')
                <div class="card mb-4">
                    <div class="card-header">{{ __('admin.social_media') }}</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="social_facebook" class="form-label"><i class="bi bi-facebook text-primary me-1"></i>Facebook</label>
                                    <input type="url" class="form-control" id="social_facebook" name="settings[social_facebook]" value="{{ $settings['social_facebook'] ?? '' }}" placeholder="https://facebook.com/...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="social_instagram" class="form-label"><i class="bi bi-instagram text-danger me-1"></i>Instagram</label>
                                    <input type="url" class="form-control" id="social_instagram" name="settings[social_instagram]" value="{{ $settings['social_instagram'] ?? '' }}" placeholder="https://instagram.com/...">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="social_twitter" class="form-label"><i class="bi bi-twitter text-info me-1"></i>Twitter / X</label>
                                    <input type="url" class="form-control" id="social_twitter" name="settings[social_twitter]" value="{{ $settings['social_twitter'] ?? '' }}" placeholder="https://twitter.com/...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="social_youtube" class="form-label"><i class="bi bi-youtube text-danger me-1"></i>YouTube</label>
                                    <input type="url" class="form-control" id="social_youtube" name="settings[social_youtube]" value="{{ $settings['social_youtube'] ?? '' }}" placeholder="https://youtube.com/...">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="social_linkedin" class="form-label"><i class="bi bi-linkedin text-primary me-1"></i>LinkedIn</label>
                                    <input type="url" class="form-control" id="social_linkedin" name="settings[social_linkedin]" value="{{ $settings['social_linkedin'] ?? '' }}" placeholder="https://linkedin.com/...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="social_tiktok" class="form-label"><i class="bi bi-tiktok me-1"></i>TikTok</label>
                                    <input type="url" class="form-control" id="social_tiktok" name="settings[social_tiktok]" value="{{ $settings['social_tiktok'] ?? '' }}" placeholder="https://tiktok.com/...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>{{ __('admin.save_settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
