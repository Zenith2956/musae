import {
    queryParams,
    type RouteQueryOptions,
    type RouteDefinition,
    type RouteFormDefinition
} from './../wayfinder'

/**
 * Générateur générique de routes
 */
function createRoute(
    url: string,
    methods: readonly string[]
) {
    const baseUrl = url

    const route = ((options?: RouteQueryOptions) => ({
        url: route.url(options),
        method: methods[0],
    })) as any

    // Définition
    route.definition = {
        methods,
        url: baseUrl,
    } satisfies RouteDefinition<typeof methods>

    // URL builder
    route.url = (options?: RouteQueryOptions) =>
        baseUrl + queryParams(options)

    // Méthodes HTTP (get, post, head…)
    methods.forEach(method => {
        route[method] = (options?: RouteQueryOptions) => ({
            url: route.url(options),
            method,
        })
    })

    // Form builder
    const form = (options?: RouteQueryOptions) => ({
        action: route.url(options),
        method: methods[0],
    })

    // Form.get / Form.post / Form.head
    methods.forEach(method => {
        form[method] = (options?: RouteQueryOptions) => ({
            action: route.url(
                method === 'head'
                    ? {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: method.toUpperCase(),
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }
                    : options
            ),
            method: methods[0],
        })
    })

    route.form = form

    return route
}

/**
 * Déclaration de toutes les routes
 * (1 ligne = 1 route)
 */
export const login       = createRoute('/login',       ['get', 'head'])
export const logout      = createRoute('/logout',      ['post'])
export const register    = createRoute('/register',    ['get', 'head'])
export const home        = createRoute('/',            ['get', 'head'])
export const dashboard   = createRoute('/dashboard',   ['get', 'head'])
export const calendar    = createRoute('/calendar',    ['get', 'head'])
export const library     = createRoute('/library',     ['get', 'head'])
export const historique  = createRoute('/historique',  ['get', 'head'])
export const messagerie  = createRoute('/messagerie',  ['get', 'head'])
