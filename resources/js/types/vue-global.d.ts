import '@vue/runtime-core'

declare module '@vue/runtime-core' {
    export interface ComponentCustomProperties {
        $formatCurrency: (amount: number | string | undefined | null) => string;
    }
}
