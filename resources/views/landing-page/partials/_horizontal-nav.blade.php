<!-- Horizontal Menu Start -->
<nav id="navbar_main" class="mobile-offcanvas nav navbar navbar-expand-xl hover-nav horizontal-nav py-xl-0">
    <div class="container-fluid p-lg-0">
        <div class="offcanvas-header px-0">
            <div class="navbar-brand ms-3">
                @include('landing-page.components.widgets.logo')
            </div>
            <button class="btn-close float-end px-3"></button>
        </div>

        @php
            $headerSection = App\Models\FrontendSetting::where('key', 'heder-menu-setting')->first();
            $sectionData = $headerSection ? json_decode($headerSection->value, true) : null;
            $settings = App\Models\Setting::whereIn('type', ['service-configurations','OTHER_SETTING'])
                ->whereIn('key', ['service-configurations', 'OTHER_SETTING'])
                ->get()
                ->keyBy('type');

            $serviceconfig = $settings->has('service-configurations') ? json_decode($settings['service-configurations']->value) : null;
            $othersetting = $settings->has('OTHER_SETTING') ? json_decode($settings['OTHER_SETTING']->value) : null;
        @endphp

        @if ($sectionData && isset($sectionData['header_setting']) && $sectionData['header_setting'] == 1)
        <ul class="navbar-nav iq-nav-menu list-unstyled" id="header-menu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('frontend.index') ? 'active' : '' }}" href="{{ route('frontend.index') }}">{{__('landingpage.home')}}</a>
            </li>

            {{-- Menu Categories Will be Fetched via AJAX --}}
            <li id="menu-placeholder"></li>
            
            @if( isset($sectionData['categories']) && $sectionData['categories'] == 1)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('category.*') ? 'active' : '' }}" href="{{ route('category.list') }}">{{__('landingpage.categories')}}</a>
            </li>
            @endif
            @if( isset($sectionData['service']) && $sectionData['service'] == 1)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('service.*') ? 'active' : '' }}" href="{{ route('service.list') }}">{{__('landingpage.services')}}</a>
            </li>
            @endif
            @if(optional($othersetting)->blog  == 1)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}" href="{{ route('blog.list') }}">{{__('landingpage.blogs')}}</a>
            </li>
            @endif
            @if(isset($sectionData['provider']) && $sectionData['provider'] == 1)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('frontend.provider.*') ? 'active' : '' }}" href="{{ route('frontend.provider') }}">{{__('landingpage.providers')}}</a>
            </li>
            @endif
            @if(auth()->check() && auth()->user()->user_type == 'user' && isset($sectionData['bookings']) && $sectionData['bookings'] == 1)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('booking.*') ? 'active' : '' }}" href="{{ route('booking.list') }}">{{__('landingpage.bookings')}}</a>
                </li>
            @endif
        </ul>
        @endif
    </div>
    <!-- container-fluid.// -->
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    fetchMenuCategories();

    function fetchMenuCategories() {
        fetch("{{ url('/menu-fetch') }}")
            .then(response => response.json())
            .then(menuCategories => {
                let menuHtml = "";
                menuCategories.forEach(menu => {
                    let submenuHtml = "";
                    if (menu.submenus.length > 0) {
                        submenuHtml += `<ul class="submenu list-unstyled" id="submenu${menu.id}">`;
                        menu.submenus.forEach(submenu => {
                            submenuHtml += `
                                <li>
                                    <form action="{{ route('category.list.filter') }}" method="POST" style="display: none;" id="submenuForm${submenu.id}">
                                        @csrf
                                        <input type="hidden" name="submenu_id" value="${submenu.id}">
                                    </form>
                                    <a class="dropdown-item" href="#" onclick="document.getElementById('submenuForm${submenu.id}').submit();">
                                        ${submenu.name}
                                    </a>
                                </li>`;
                        });
                        submenuHtml += `</ul>`;
                    }

                    menuHtml += `
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="menu${menu.id}" role="button">
                                ${menu.name}
                                <span class="toggle-arrow toggle-arrow-1" style="display: none;">▼</span>
                            </a>
                            ${submenuHtml}
                        </li>`;
                });

                // Insert menu items before the placeholder
                document.getElementById("menu-placeholder").insertAdjacentHTML("beforebegin", menuHtml);
            })
            .catch(error => console.error("Error fetching menu categories:", error));
    }

    // Toggle Dropdown Menu Functionality
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("dropdown-toggle")) {
            event.preventDefault();
            let submenu = event.target.nextElementSibling;
            let toggleArrow = event.target.querySelector(".toggle-arrow");

            if (submenu.classList.contains("show")) {
                submenu.classList.remove("show");
                toggleArrow.textContent = "▼";
            } else {
                document.querySelectorAll(".submenu").forEach(sub => sub.classList.remove("show"));
                document.querySelectorAll(".toggle-arrow").forEach(arrow => arrow.textContent = "▼");

                submenu.classList.add("show");
                toggleArrow.textContent = "▲";
            }
        }
    });
});

</script>
