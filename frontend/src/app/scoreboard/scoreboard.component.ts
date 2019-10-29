import { Component, Renderer2, OnDestroy } from '@angular/core';
import { AuthService } from '@app/auth/auth.service';
import { Router } from '@angular/router';
import { environment } from '../../environments/environment';

@Component({
    selector: 'app-scoreboard-root',
    templateUrl: './scoreboard.component.html',
    styleUrls: ['./scoreboard.component.css'],
})
export class ScoreboardComponent implements OnDestroy {
    public title;

    constructor (
        private render: Renderer2,
        public auth: AuthService,
        public router: Router
    ) {
        this.title = environment.APP_NAME;
        this.render.addClass(document.body, 'scoremodule-module');
    }

    ngOnDestroy () {
      this.render.removeClass(document.body, 'scoremodule-module');
    }
}
