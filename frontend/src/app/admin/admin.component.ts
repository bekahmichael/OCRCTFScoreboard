import { Component, Renderer2, OnDestroy, HostBinding } from '@angular/core';
import { AuthService } from '@app/auth/auth.service';
import { Router } from '@angular/router';
import { environment } from '../../environments/environment';

@Component({
  selector: 'app-admin-root',
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.css'],
})
export class AdminComponent implements OnDestroy {
    public title;
    sideBarToggleStorageKey = '__sideBarToggleStorageKey';

    @HostBinding('class.sidebar-compact') 'isCompactSidebar()';
    @HostBinding('class.no-logged') '!isLogged()';

    constructor (
        private render: Renderer2,
        private auth: AuthService,
        public router: Router
    ) {
        this.title = environment.APP_NAME;
        this.render.addClass(document.body, 'admin-module');
    }

    isLogged() {
        return !this.auth.isGuest;
    }

    logOut() {
        this.auth.logout();
        this.router.navigate(['/login']);
    }

    isCompactSidebar(): boolean {
        return !!parseInt(localStorage.getItem(this.sideBarToggleStorageKey), 10);
    }

    sidebarToggle () {
        localStorage.setItem(this.sideBarToggleStorageKey, this.isCompactSidebar() ? '0' : '1');
    }

    showUsersMenu() {
        return this.auth.can('admin');
    }

    ngOnDestroy () {
        this.render.removeClass(document.body, 'admin-module');
    }
}
