import { Component, HostBinding } from '@angular/core';
import { AuthService } from './auth/auth.service';
import { Router } from '@angular/router';
import { environment } from '../environments/environment';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
})
export class AppComponent {
    public title;

    @HostBinding('class.no-logged') '!isLogged()';

    constructor (
        private auth: AuthService,
        public router: Router
    ) {
        this.title = environment.APP_NAME;
    }

    isLogged() {
        return !this.auth.isGuest;
    }

    logOut() {
        this.auth.logout();
        this.router.navigate(['/login']);
    }
}
