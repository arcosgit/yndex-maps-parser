import { computed } from "vue";

export default function setColorRating(rating){
    return computed(() => {
        if (rating === null || rating === '0' || rating === '0.0' || rating === 0) {
            return 'text-gray-400';
        }
        const floatRating = parseFloat(String(rating).replace(',', '.'))

        if (isNaN(floatRating)) {
            return 'text-gray-400';
        }

        if (floatRating >= 4.0) {
            return 'text-emerald-500';
        }

        if (floatRating >= 3.0) {
            return 'text-amber-500';
        }
        return 'text-red-500';
    });
}
