import {
    Component,
    Injectable,
    Input
} from '@angular/core';

@Component({
    selector: 'wo-preview-image',
    templateUrl: 'wo-preview-image.component.html',
    styleUrls: ['wo-preview-image.component.css'],
})

@Injectable()
export class WoPreviewImageComponent {
    @Input() woFullSrc: string;

    isLoading = false;
    isDisplay = false;
    imageWidth = null;
    imageHeight = null;
    adaptiveWidth = 0;
    adaptiveHeight = 0;
    wrapperOpacity = 0;

    constructor () {
    }

    showPreview(event) {
        this.adaptiveWidth = 0;
        this.adaptiveHeight = 0;
        event.stopPropagation();
        if (typeof this.woFullSrc !== 'undefined' && this.woFullSrc !== null && this.isDisplay === false) {
            this.isLoading = true;
            const newImg = new Image;
            newImg.onload = () => {
                this.isLoading = false;
                this.isDisplay = true;
                this.imageWidth = newImg.naturalWidth;
                this.imageHeight = newImg.naturalHeight;
                setTimeout(() => {
                    this.adaptiveSize();
                    this.wrapperOpacity = 1;
                }, 50);
                document.body.addEventListener('keydown',  (e) => {
                    if (this.isDisplay === true) {
                        if (e.keyCode === 27) {
                            this.close(e);
                        }
                    }
                });​​​​​​​
                window.onresize = () => {
                    if (this.isDisplay === true) {
                        this.adaptiveSize();
                    }
                };
            };
            newImg.src = this.woFullSrc;
        }
    }

    adaptiveSize() {
        if (this.imageWidth > this.imageHeight) {
            this.adaptiveWidth = 0.7 * window.innerWidth;
            this.adaptiveHeight = (this.imageHeight / this.imageWidth) * this.adaptiveWidth;

            if (this.adaptiveHeight > window.innerHeight) {
                this.adaptiveHeight = 0.7 * window.innerHeight;
                this.adaptiveWidth = (this.imageWidth / this.imageHeight) * this.adaptiveHeight;
            }
        } else {
            this.adaptiveHeight = 0.7 * window.innerHeight;
            this.adaptiveWidth = (this.imageWidth / this.imageHeight) * this.adaptiveHeight;

            if (this.adaptiveWidth > window.innerWidth) {
                this.adaptiveWidth = 0.7 * window.innerWidth;
                this.adaptiveHeight = (this.imageHeight / this.imageWidth) * this.adaptiveWidth;
            }
        }
    }

    close(event) {
        event.stopPropagation();
        this.isDisplay = false;
        this.wrapperOpacity = 0;
    }

    clickByImage(event) {
        event.stopPropagation();
    }
}
