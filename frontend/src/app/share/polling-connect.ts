import { Type } from '@angular/core';
import { Subject } from 'rxjs/Subject';
import { HttpService } from '../core/http.service';
import { Observable } from 'rxjs/Rx';
import "rxjs/add/observable/interval";
import "rxjs/add/operator/takeWhile";

export class PollingConnect {
  /** Subject for notifying the user that the dialog has finished closing. */
  private _onMessage: Subject<any> = new Subject();
  private _onClose: Subject<any> = new Subject();
  private isEnable = true;
  private inProgress = false;
  private lastHash = '';

  constructor(
    public url: string,
    private service,
    private http: HttpService,
    private body,
  ) {}

  start() {
    Observable
        .interval(5 * 1000)
        .startWith(0)
        .takeWhile(() => this.isEnable && !this.inProgress)
        .subscribe(i => {
            this.doRequest()
            .then(res => {
                setTimeout(() => {
                  this.inProgress = false;
                  if (res['hash']) {
                      this.lastHash = res['hash'];
                  }
                  this._onMessage.next(res);
                  this.start();
                }, 1000);
            }).catch(error => {
                this.inProgress = false;
                this.close(error);
                console.error(error);
            });
        });
  }

  close(result) {
    this.isEnable = false;
    this._onClose.next(result);
    this._onClose.complete();
    this.service.close(this);
  }

  onMessage() {
    return this._onMessage;
  }

  doRequest() {
      this.inProgress = true;
      return this.http.send(this.url, {hash: this.lastHash}, this.body);
  }

  onClose() {
      return this._onClose;
  }
}
