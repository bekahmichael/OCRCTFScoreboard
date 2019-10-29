import { Component } from '@angular/core';
import { AuthService } from '@app/auth/auth.service';
import { Router } from '@angular/router';

@Component({
    selector: 'app-user-menu',
    templateUrl: 'user-menu.component.html',
    styleUrls: ['user-menu.component.css'],
})
export class UserMenuComponent {
    public authService;
    public isOpen = false;

    constructor (
        private auth: AuthService,
        private router: Router
    ) {
        this.authService = this.auth;

        document.addEventListener('mouseup', (event) => {
            const toggleState = this.isOpen;
            setTimeout(() => {
                if (toggleState === true) {
                    this.isOpen = false;
                }
            }, 1);
        });
    }

    toggleMenu () {
        this.isOpen = !this.isOpen;
    }

    myProfile() {
        this.router.navigate(['/', this.auth.user['id']]);
    }

    logOut() {
        this.authService.logout();
        this.router.navigate(['/login']);
    }
}
