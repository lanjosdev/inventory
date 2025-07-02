import { FormLogin } from "@/components/FormLogin/FormLogin";


export default function LoginPage() {

    return (
        <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center p-4">
            <div className="w-full max-w-md">
                <div className="text-center mb-8">
                    <h1 className="text-3xl font-bold text-slate-900 mb-2">Faça seu Login</h1>
                    <p className="text-slate-600">Entre na sua conta para continuar</p>
                </div>
                
                <FormLogin />
            </div>
        </div>
    );
}