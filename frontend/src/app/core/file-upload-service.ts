import { Injectable } from '@angular/core';
import { Observable } from 'rxjs/Observable';
import { AuthService } from '../auth/auth.service';
import 'rxjs/add/operator/share';
@Injectable()
export class FileUploadService {
    /**
     * @param Observable<number>
     */
    private progress$: Observable<number>;
    private progress = 0;
    private progressObserver: any;

    /**
     * Set interval for frequency with which Observable inside Promise will share data with subscribers.
     */
    private static setUploadUpdateInterval (interval: number): void {
        setInterval(() => {}, interval);
    }

    constructor (private auth: AuthService) {
        this.progress$ = new Observable(observer => {
            this.progressObserver = observer;
        });
    }

    public getProgress (): Observable<number> {
        return this.progress$;
    }

    /**
     * Upload files through XMLHttpRequest
     */
    public upload (url: string, files: File[], params = null): Promise<any> {
        return new Promise((resolve, reject) => {
            const formData: FormData = new FormData();
            const xhr: XMLHttpRequest = new XMLHttpRequest();
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i], files[i].name);
            }

            if (params !== null) {
                this.buildFormData(formData, params);
            }

            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        resolve(JSON.parse(xhr.response));
                    } else {
                        reject(xhr.response);
                    }
                }
            };
            FileUploadService.setUploadUpdateInterval(100);
            xhr.upload.onprogress = (event) => {
                this.progress = Math.round(event.loaded / event.total * 100);
                if (this.progressObserver) {
                    this.progressObserver.next(this.progress);
                }
            };
            xhr.open('POST', url, true);
            xhr.setRequestHeader('Authorization', 'Bearer ' + this.auth.getToken());
            xhr.send(formData);
        });
    }

    /**
     * Build data for FormData object.
     * @param formData object
     * @param data object
     * @param parentKey string
     */
    buildFormData(formData, data, parentKey = null) {
        if (data && typeof data === 'object' && !(data instanceof Date) && !(data instanceof File)) {
            Object.keys(data).forEach(key => {
                this.buildFormData(formData, data[key], parentKey ? `${parentKey}[${key}]` : key);
            });
        } else {
            const value = data == null ? '' : data;
            formData.append(parentKey, value);
        }
    }
}
