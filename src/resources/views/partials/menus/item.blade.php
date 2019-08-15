{{--
@if (!((admix_cannot('view', '\Agenciafmd\{{ Pacotes }}\{{ Pacote }}')) && (admix_cannot('view', '\Agenciafmd\{{ Pacotes }}\{{ Tipo }}'))))
    <li class="nav-item">
        <a class="nav-link @if (admix_is_active(route('admix.{{ pacotes }}.index')) || admix_is_active(route('admix.{{ tipos }}.index'))) active @endif"
           href="#sidebar-settings" data-toggle="collapse" data-parent="#menu" role="button"
           aria-expanded="{{ (admix_is_active(route('admix.{{ pacotes }}.index')) || admix_is_active(route('admix.{{ tipos }}.index'))) ? 'true' : 'false' }}">
            <span class="nav-icon">
                <i class="icon fe-settings"></i>
            </span>
            <span class="nav-text">
                {{ config('local-{{ pacotes  }}.name') }}
            </span>
        </a>
        <div
            class="navbar-subnav collapse @if (admix_is_active(route('admix.{{ pacotes }}.index')) || admix_is_active(route('admix.{{ tipos }}.index')) ) show @endif"
            id="sidebar-settings">
            <ul class="nav">
                @can('view', '\Agenciafmd\{{ Pacotes }}\{{ Pacote }}')
                    <li class="nav-item">
                        <a class="nav-link {{ admix_is_active(route('admix.{{ pacotes }}.index')) ? 'active' : '' }}"
                           href="{{ route('admix.{{ pacotes }}.index') }}">
                            <span class="nav-icon">
                                <i class="icon fe-minus"></i>
                            </span>
                            <span class="nav-text">
                                {{ config('local-{{ pacotes  }}.name') }}
                            </span>
                        </a>
                    </li>
                @endcan
                @can('view', '\Agenciafmd\{{ Pacotes }}\{{ Tipo }}')
                    <li class="nav-item">
                        <a class="nav-link {{ admix_is_active(route('admix.{{ pacotes }}.{{ tipos }}.index')) ? 'active' : '' }}"
                           href="{{ route('admix.{{ pacotes }}.{{ tipos }}.index') }}">
                            <span class="nav-icon">
                                <i class="icon fe-minus"></i>
                            </span>
                            <span class="nav-text">
                                {{ config('local-tags.{{ tipos  }}.name') }}
                            </span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </li>
@endif
--}}
