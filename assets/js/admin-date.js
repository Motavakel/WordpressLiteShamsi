class JalaliDate {
    static g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    static j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

    static jalaliToGregorian(j_y, j_m, j_d) {
        j_y = parseInt(j_y);
        j_m = parseInt(j_m);
        j_d = parseInt(j_d);
        let jy = j_y - 979;
        let jm = j_m - 1;
        let jd = j_d - 1;

        let j_day_no = 365 * jy + Math.floor(jy / 33) * 8 + Math.floor((jy % 33 + 3) / 4);
        for (let i = 0; i < jm; ++i) j_day_no += JalaliDate.j_days_in_month[i];

        j_day_no += jd;

        let g_day_no = j_day_no + 79;

        let gy = 1600 + 400 * Math.floor(g_day_no / 146097);
        g_day_no %= 146097;

        let leap = true;
        if (g_day_no >= 36525) {
            g_day_no--;
            gy += 100 * Math.floor(g_day_no / 36524);
            g_day_no %= 36524;

            if (g_day_no >= 365) g_day_no++;
            else leap = false;
        }

        gy += 4 * Math.floor(g_day_no / 1461);
        g_day_no %= 1461;

        if (g_day_no >= 366) {
            leap = false;
            g_day_no--;
            gy += Math.floor(g_day_no / 365);
            g_day_no %= 365;
        }

        let gm;
        for (gm = 0; g_day_no >= JalaliDate.g_days_in_month[gm] + (gm === 1 && leap); gm++) {
            g_day_no -= JalaliDate.g_days_in_month[gm] + (gm === 1 && leap);
        }
        let gd = g_day_no + 1;

        return [gy, gm + 1, gd];
    }

    static checkDate(j_y, j_m, j_d) {
        return !(j_y < 0 || j_y > 32767 || j_m < 1 || j_m > 12 || j_d < 1 ||
            j_d > (JalaliDate.j_days_in_month[j_m - 1] + (j_m === 12 && !((j_y - 979) % 33 % 4))));
    }

    static gregorianToJalali(g_y, g_m, g_d) {
        g_y = parseInt(g_y);
        g_m = parseInt(g_m);
        g_d = parseInt(g_d);
        let gy = g_y - 1600;
        let gm = g_m - 1;
        let gd = g_d - 1;

        let g_day_no = 365 * gy + Math.floor((gy + 3) / 4) - Math.floor((gy + 99) / 100) + Math.floor((gy + 399) / 400);

        for (let i = 0; i < gm; ++i) g_day_no += JalaliDate.g_days_in_month[i];
        if (gm > 1 && ((gy % 4 === 0 && gy % 100 !== 0) || (gy % 400 === 0))) {
            g_day_no++;
        }
        g_day_no += gd;

        let j_day_no = g_day_no - 79;

        let j_np = Math.floor(j_day_no / 12053);
        j_day_no %= 12053;

        let jy = 979 + 33 * j_np + 4 * Math.floor(j_day_no / 1461);
        j_day_no %= 1461;

        if (j_day_no >= 366) {
            jy += Math.floor((j_day_no - 1) / 365);
            j_day_no = (j_day_no - 1) % 365;
        }

        let jm;
        for (jm = 0; jm < 11 && j_day_no >= JalaliDate.j_days_in_month[jm]; ++jm) {
            j_day_no -= JalaliDate.j_days_in_month[jm];
        }
        let jd = j_day_no + 1;

        return [jy, jm + 1, jd];
    }
}
