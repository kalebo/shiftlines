#!/usr/bin/python
hours = ([1,3],[9,12],[13,14],[13,15],[14,17],[21,23])
def maphours(hours, pph=1, start=8, end=17):
    buffer = [0] * (end - start) * pph
    for period in hours:
        if period[0] < start:
            period[0] = start
        if period[1] > end:
            period[1] = end
        print period
        i = period[0] * pph
        while (i < period[1] * pph):
            for j in xrange(pph):
                buffer[i - start * pph] = 1
                i += 1
    return buffer
