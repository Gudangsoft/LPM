@if(session()->has('impersonator_id'))
<div class="impersonation-banner">
    <div class="impersonation-banner__inner">
        <span class="impersonation-banner__text">
            <i class="bi bi-incognito"></i>
            {{ __('admin.impersonating', ['name' => auth()->user()->name]) }}
        </span>
        <form action="{{ route('impersonate.leave') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="impersonation-banner__btn">
                <i class="bi bi-box-arrow-left"></i>
                {{ __('admin.stop_impersonating') }}
            </button>
        </form>
    </div>
</div>
<style>
    .impersonation-banner {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 3000;
        background: #b45309;
        color: #fff;
        box-shadow: 0 -2px 12px rgba(0, 0, 0, .2);
    }
    .impersonation-banner__inner {
        max-width: 1320px;
        margin: 0 auto;
        padding: 9px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        font-size: .88rem;
    }
    .impersonation-banner__text { font-weight: 500; }
    .impersonation-banner__text i { margin-right: 6px; }
    .impersonation-banner__btn {
        background: #fff;
        color: #b45309;
        border: 0;
        border-radius: 6px;
        padding: 5px 14px;
        font-weight: 600;
        font-size: .82rem;
        cursor: pointer;
        white-space: nowrap;
    }
    .impersonation-banner__btn:hover { background: #fde68a; }
    .impersonation-banner__btn i { margin-right: 4px; }
</style>
@endif
