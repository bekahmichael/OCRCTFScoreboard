import { Injectable } from '@angular/core';
import { HttpService } from '../core/http.service';
import { environment } from '../../environments/environment';

@Injectable()
export class ApiAdminService {
    public baseUrl;

    constructor(private http: HttpService) {
        this.baseUrl = environment.API_BASE_URL + '/admin';
    }

    send(url, get = null, post = null) {
        return this.http.send(this.baseUrl + '/' + url, get, post);
    }
}
