<div class="page-header">
    <div class="header-wrapper row m-0">
        <form class="form-inline search-full col" action="#" method="get">
            <div class="form-group w-100">
                <div class="Typeahead Typeahead--twitterUsers">
                    <div class="u-posRelative">
                        <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text" placeholder="Search Cuba .." name="q" title="" autofocus>
                        <div class="spinner-border Typeahead-spinner" role="status"><span class="sr-only">Loading...</span></div><i class="close-search" data-feather="x"></i>
                    </div>
                    <div class="Typeahead-menu"></div>
                </div>
            </div>
        </form>
        <div class="header-logo-wrapper col-auto p-0">
            <div class="logo-wrapper"><a href="{{route('getdashboard')}}"><img class="img-fluid" src="{{asset('../assets/images/logo/logo.png')}}" alt=""></a></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i></div>
        </div>
        <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
            <marquee>
                <div class="notification-slider">
                    <div class="d-flex h-100"> <img src="{{asset('../assets/images/giftools.gif')}}" alt="gif">
                        <h6 class="mb-0 f-w-500"><span class="font-primary">Welcome to Vanvil PG Dashboard </span></h6><i class="icon-arrow-top-right f-light"></i>
                    </div>
                </div>
            </marquee>
        </div>
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
            <ul class="nav-menus">
                <!-- <li class="language-nav">
                    <div class="translate_wrapper">
                        <div class="current_lang">
                            <div class="lang"><i class="flag-icon flag-icon-us"></i><span class="lang-txt">EN </span></div>
                        </div>
                        <div class="more_lang">
                        <div class="lang selected" data-value="en"><i class="flag-icon flag-icon-us"></i><span class="lang-txt">English<span> (US)</span></span></div>
                        {{--  <div class="lang" data-value="in"><i class="flag-icon flag-icon-in"></i><span class="lang-txt">Hindi</span></div>
                            <div class="lang" data-value="de"><i class="flag-icon flag-icon-de"></i><span class="lang-txt">Deutsch</span></div>
                            <div class="lang" data-value="es"><i class="flag-icon flag-icon-es"></i><span class="lang-txt">Español</span></div>
                            <div class="lang" data-value="fr"><i class="flag-icon flag-icon-fr"></i><span class="lang-txt">Français</span></div>
                            <div class="lang" data-value="pt"><i class="flag-icon flag-icon-pt"></i><span class="lang-txt">Português<span> (BR)</span></span></div>
                            <div class="lang" data-value="cn"><i class="flag-icon flag-icon-cn"></i><span class="lang-txt">简体中文</span></div>
                            <div class="lang" data-value="ae"><i class="flag-icon flag-icon-ae"></i><span class="lang-txt">لعربية <span> (ae)</span></span></div>--}}
                        </div>
                    </div>
                </li> -->
                <!-- <li>
                    <div class="mode">
                        <svg>
                            <use href="{{asset('../assets/svg/icon-sprite.svg#moon')}}"></use>
                        </svg>
                    </div>
                </li> -->
            
                


                @if (Myhelper::hasRole('admin'))
               <li class="nav-item nav-icon mt-2">
                  <button type="button" class="btn btn-sm py-1 px-2 btn-primary" data-bs-toggle="modal" data-bs-target="#walletLoadModal">Load Wallet</button>

               </li>
               @endif
                <li class="onhover-dropdown">
                    <div >
                       <i class="fa fa-folder-open-o fs-5 mt-1"></i>
                    </div>
                    <div class="onhover-show-div notification-dropdown">
                        <h6 class="f-18 mb-0 dropdown-title">Wallet Balance </h6>
                        <ul>
                            <li class="b-l-primary border-4">
                                <p>Main Wallet<span class="font-primary fs-6">&#8377; <span class="fs-6" id="maiwalletbalance"> {{Auth::user()->mainwallet}}  </span> /-</span></p>
                            </li>
                            <li class="b-l-success border-4">
                                <p>API Wallet<span class="font-success fs-6">&#8377; <span class="fs-6" id="aepsbalance">  </span> /-</span></p>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="profile-nav onhover-dropdown pe-0 py-0">
                    <div class="media profile-media"><img class="b-r-10" src="{{asset('../assets/images/dashboard/profile.png')}}" alt="">
                        <div class="media-body"><span>{{ Auth::user()->name }}</span>
                            <p class="mb-0 font-roboto">{{Auth::user()->role->name}} <i class="middle fa fa-angle-down"></i></p>
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <li><a href="{{ url('profile/view') }}"><i data-feather="user"></i><span>Profile </span></a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{route('logout')}}" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    <i data-feather="log-out"> </i><span>Log Out</span></a>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <script class="result-template" type="text/x-handlebars-template">
            <div class="ProfileCard u-cf">
            <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
            <div class="ProfileCard-details">
            <div class="ProfileCard-realName">{{ Auth::user()->name }}</div>
            </div>
            </div>
          </script>
        <script class="empty-template" type="text/x-handlebars-template"><div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div></script>
    </div>
</div>
