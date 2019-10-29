import { Component, Renderer2, OnDestroy } from '@angular/core';
import { AuthService } from '@app/auth/auth.service';
import { Router } from '@angular/router';
import { environment } from '../../environments/environment';

@Component({
    selector: 'app-public-root',
    templateUrl: './public.component.html',
    styleUrls: ['./public.component.css'],
})
export class PublicComponent implements OnDestroy {
    public title;

    constructor (
        private render: Renderer2,
        public auth: AuthService,
        public router: Router
    ) {
        this.title = environment.APP_NAME;
        this.render.addClass(document.body, 'public-module');
    }

    doLogOut() {
        this.auth.logout();
        this.router.navigate(['login']);
    }

    ngOnDestroy () {
      this.render.removeClass(document.body, 'public-module');
    }
}
