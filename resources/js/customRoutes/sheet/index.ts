import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\SheetController::detail
 * @see app/Http/Controllers/SheetController.php:22
 * @route '/sheet/{sheet}'
 */
export const detail = (args: { sheet: number | { id: number } } | [sheet: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})

detail.definition = {
    methods: ["get","head"],
    url: '/sheet/{sheet}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SheetController::detail
 * @see app/Http/Controllers/SheetController.php:22
 * @route '/sheet/{sheet}'
 */
detail.url = (args: { sheet: number | { id: number } } | [sheet: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { sheet: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { sheet: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    sheet: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        sheet: typeof args.sheet === 'object'
                ? args.sheet.id
                : args.sheet,
                }

    return detail.definition.url
            .replace('{sheet}', parsedArgs.sheet.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SheetController::detail
 * @see app/Http/Controllers/SheetController.php:22
 * @route '/sheet/{sheet}'
 */
detail.get = (args: { sheet: number | { id: number } } | [sheet: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SheetController::detail
 * @see app/Http/Controllers/SheetController.php:22
 * @route '/sheet/{sheet}'
 */
detail.head = (args: { sheet: number | { id: number } } | [sheet: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detail.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\SheetController::detail
 * @see app/Http/Controllers/SheetController.php:22
 * @route '/sheet/{sheet}'
 */
    const detailForm = (args: { sheet: number | { id: number } } | [sheet: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: detail.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\SheetController::detail
 * @see app/Http/Controllers/SheetController.php:22
 * @route '/sheet/{sheet}'
 */
        detailForm.get = (args: { sheet: number | { id: number } } | [sheet: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: detail.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\SheetController::detail
 * @see app/Http/Controllers/SheetController.php:22
 * @route '/sheet/{sheet}'
 */
        detailForm.head = (args: { sheet: number | { id: number } } | [sheet: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: detail.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    detail.form = detailForm
const sheet = {
    detail: Object.assign(detail, detail),
}

export default sheet