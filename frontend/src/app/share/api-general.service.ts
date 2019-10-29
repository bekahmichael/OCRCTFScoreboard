import { Injectable } from '@angular/core';
import { HttpService } from '../core/http.service';
import { environment } from '../../environments/environment';

@Injectable()
export class ApiGeneralService {
    constructor(private http: HttpService) {}

    send(url, get = null, post = null) {
        return this.http.send(environment.API_BASE_URL + '/general/' + url, get, post);
    }
}
