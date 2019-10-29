import { Injectable } from '@angular/core';

@Injectable()
export class AppStorageService {
    private prefix = '_app';

    private getPrefixedKey (key: string) {
        return this.prefix + key;
    }

    setParam (key: string, value: any) {
        const query_key = this.getPrefixedKey(key);

        try {
            localStorage.setItem(query_key, JSON.stringify({'data': value}));
        } catch (e) {
            if (console) {
                console.warn(
                    'localStorage didn\'t successfully save the \'{' + key + ': ' + value + '}\' pair, because the localStorage is full.'
                );
            }
        }
    }

    getParam (key: string, missing: any = null) {
        const query_key = this.getPrefixedKey(key);
        let value;

        try {
            value = JSON.parse(localStorage.getItem(query_key));
        } catch (e) {
            if (localStorage[query_key]) {
                value = {data: localStorage.getItem(query_key)};
            } else {
                value = null;
            }
        }

        if (value === null) {
            return missing;
        } else if (typeof value === 'object' && typeof value.data !== 'undefined') {
            return value.data;
        } else {
            return missing;
        }
    }
}
