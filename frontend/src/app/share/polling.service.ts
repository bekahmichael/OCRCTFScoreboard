import { Injectable } from '@angular/core';
import { HttpService } from '../core/http.service';
import { AuthService } from '../auth/auth.service';
import { environment } from '../../environments/environment';
import { PollingConnect } from './polling-connect';

@Injectable()
export class PollingService {
  static instance: PollingService;

    constructor(
        private http: HttpService,
        private auth: AuthService,
    ) {
        return PollingService.instance = PollingService.instance || this;
    }

    private url;
    private connections = [];

    open(url, data) {
        let ref = new PollingConnect(url, this, this.http, data);
        this.connections.push(ref);
        return ref;
    }

    close (dialog, result) {
        this.connections.splice(this.connections.indexOf(dialog), 1);
    }
}
