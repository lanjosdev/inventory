import { NextResponse } from 'next/server'
import type { NextRequest } from 'next/server'

// Nomes das rotas
const LOGIN_ROUTE = '/login'
const DASHBOARD_ROUTE = '/dashboard'

// Rotas públicas que não exigem autenticação
const PUBLIC_ROUTES = [LOGIN_ROUTE, '/']

export function middleware(request: NextRequest) {
    const { pathname } = request.nextUrl
    const authTokenName = process.env.NEXT_COOKIE_AUTH_TOKEN_NAME || 'auth_token_bizsys'
    const token = request.cookies.get(authTokenName)

    const isProtectedRoute = !PUBLIC_ROUTES.includes(pathname)

    // 1. Redireciona para /login se não houver token e a rota for protegida
    if (!token && isProtectedRoute) {
        const loginUrl = new URL(LOGIN_ROUTE, request.url)
        return NextResponse.redirect(loginUrl)
    }

    // 2. Redireciona para /dashboard se houver token e o usuário tentar acessar /login
    if (token && pathname === LOGIN_ROUTE) {
        const dashboardUrl = new URL(DASHBOARD_ROUTE, request.url)
        return NextResponse.redirect(dashboardUrl)
    }

    // 3. Permite o acesso se nenhuma das condições acima for atendida
    return NextResponse.next()
}

// Configuração do matcher para definir em quais rotas o middleware será executado
export const config = {
    matcher: [
        /*
         * Corresponde a todos os caminhos de solicitação, exceto para aqueles que começam com:
         * - api (rotas de API)
         * - _next/static (arquivos estáticos)
         * - _next/image (arquivos de otimização de imagem)
         * - favicon.ico (arquivo de favicon)
         */
        '/((?!api|_next/static|_next/image|favicon.ico).*)',
    ],
}
