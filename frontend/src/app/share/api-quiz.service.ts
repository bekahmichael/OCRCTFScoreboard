import { Injectable } from '@angular/core';
import { HttpService } from '../core/http.service';
import { environment } from '../../environments/environment';

@Injectable()
export class ApiQuizService {
    public baseUrl;

    constructor(private http: HttpService) {
        this.baseUrl = environment.API_BASE_URL + '/quiz';
    }

    send(url, get = null, post = null) {
        return this.http.send(this.baseUrl + '/' + url, get, post);
    }
}
