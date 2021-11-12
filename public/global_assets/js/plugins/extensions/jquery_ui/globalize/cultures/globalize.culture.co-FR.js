/*
 * Globalize Culture co-FR
 *
 * http://github.com/jquery/globalize
 *
 * Copyright Software Freedom Conservancy, Inc.
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * This file was generated by the Globalize Culture Generator
 * Translation: bugs found in this file need to be fixed in the generator
 */

(function (window, undefined) {

    var Globalize;

    if (typeof require !== "undefined" &&
        typeof exports !== "undefined" &&
        typeof module !== "undefined") {
        // Assume CommonJS
        Globalize = require("globalize");
    } else {
        // Global variable
        Globalize = window.Globalize;
    }

    Globalize.addCultureInfo("co-FR", "default", {
        name: "co-FR",
        englishName: "Corsican (France)",
        nativeName: "Corsu (France)",
        language: "co",
        numberFormat: {
            ",": " ",
            ".": ",",
            "NaN": "Mica numericu",
            negativeInfinity: "-Infinitu",
            positiveInfinity: "+Infinitu",
            percent: {
                ",": " ",
                ".": ","
            },
            currency: {
                pattern: ["-n $", "n $"],
                ",": " ",
                ".": ",",
                symbol: "€"
            }
        },
        calendars: {
            standard: {
                firstDay: 1,
                days: {
                    names: ["dumenica", "luni", "marti", "mercuri", "ghjovi", "venderi", "sabbatu"],
                    namesAbbr: ["dum.", "lun.", "mar.", "mer.", "ghj.", "ven.", "sab."],
                    namesShort: ["du", "lu", "ma", "me", "gh", "ve", "sa"]
                },
                months: {
                    names: ["ghjennaghju", "ferraghju", "marzu", "aprile", "maghju", "ghjunghju", "lugliu", "aostu", "settembre", "ottobre", "nuvembre", "dicembre", ""],
                    namesAbbr: ["ghje", "ferr", "marz", "apri", "magh", "ghju", "lugl", "aost", "sett", "otto", "nuve", "dice", ""]
                },
                AM: null,
                PM: null,
                eras: [{"name": "dopu J-C", "start": null, "offset": 0}],
                patterns: {
                    d: "dd/MM/yyyy",
                    D: "dddd d MMMM yyyy",
                    t: "HH:mm",
                    T: "HH:mm:ss",
                    f: "dddd d MMMM yyyy HH:mm",
                    F: "dddd d MMMM yyyy HH:mm:ss",
                    M: "d MMMM",
                    Y: "MMMM yyyy"
                }
            }
        }
    });

}(this));
