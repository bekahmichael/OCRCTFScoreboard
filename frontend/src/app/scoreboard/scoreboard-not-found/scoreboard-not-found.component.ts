import { Component, ElementRef, ViewChild, AfterContentInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';

@Component({
    templateUrl: 'scoreboard-not-found.component.html',
    styleUrls: ['scoreboard-not-found.component.css']
})
export class ScoreboardNotFoundComponent implements AfterContentInit {
    @ViewChild('canvasEl', {static: false}) canvasEl: ElementRef;
    ctx;
    buffer32;
    idata;

    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
    ) {

    }

    ngAfterContentInit(): void {
        this.canvasEl.nativeElement.width = window.innerWidth / 3;
        this.canvasEl.nativeElement.height = window.innerHeight / 3;
        this.ctx = this.canvasEl.nativeElement.getContext('2d');       // context without alpha channel.
        this.idata = this.ctx.createImageData(this.canvasEl.nativeElement.width, this.canvasEl.nativeElement.height); // create image data
        this.buffer32 = new Uint32Array(this.idata.data.buffer);  // get 32-bit view

        this.loop();
    }

    loop () {
        this.noise();
        requestAnimationFrame(() => {
            setTimeout(() => {
                this.loop();
            }, 30);
        });
    }

    noise() {
        let len = this.buffer32.length - 1;
        while (len--) {
            // tslint:disable-next-line:no-bitwise
            this.buffer32[len] = Math.random() < 0.5 ? 0 : -1 >> 0;
        }

        this.ctx.putImageData(this.idata, 0, 0);
    }
}
