import { Injectable } from '@angular/core';

class WoBreadCrumbsLink {
    iconClass?: string;
    route?: string;
    params?: any;
    text: string;
}

@Injectable()
export class WoBreadCrumbsService {
    static instance: WoBreadCrumbsService;
    public links = [];

    constructor() {
        return WoBreadCrumbsService.instance = WoBreadCrumbsService.instance || this;
    }

    setLinks(links: Array<WoBreadCrumbsLink>) {
      this.links = links;
    }
}
