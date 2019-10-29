import { PipeTransform, Pipe } from '@angular/core';

@Pipe({name: 'secondsToTime'})
export class SecondsToTimePipe implements PipeTransform {
    transform(seconds, exponent: string) {
        if (seconds === null || typeof seconds === 'undefined') {
            return '--:--:--';
        } else if (seconds < 0) {
            seconds = 0;
        }

        const date = new Date(null);
        date.setSeconds(seconds); // specify value for SECONDS here

        if (exponent === 'minutes') {
            if (seconds < 3600) {
                return date.toISOString().substr(14, 5);
            } else {
                return Math.floor(seconds / 60) + ':' + date.toISOString().substr(17, 2);
            }
        } else {
            if (seconds < 36000) {
                return date.toISOString().substr(11, 8);
            } else {
                return Math.floor(seconds / 60 / 60) + ':' + date.toISOString().substr(14, 5);
            }
        }
    }
}
