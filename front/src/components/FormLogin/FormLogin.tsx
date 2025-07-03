'use client';
import { useState } from "react";


export function FormLogin() {
    const [isLoading, setIsLoading] = useState(false); // Estado para indicar se o formulário está sendo enviado
    const [hasError, setHasError] = useState<string | null>(null);

    // Estado para armazenar os dados do formulário
    const [formData, setFormData] = useState({
        email: '',
        password: ''
    });



    // Função para lidar com mudanças nos campos do formulário
    const handleChangeForm = (event: React.ChangeEvent<HTMLInputElement>) => {
        setHasError(null); // Limpar mensagem de erro ao digitar

        // Desestruturar o id e o value do evento
        const { id, value } = event.target;

        setFormData(prevData => ({
            ...prevData,
            [id]: value
        }));
    }



    // SUBMIT FORM
    async function handleSubmitForm(event: React.FormEvent<HTMLFormElement>) {
        event.preventDefault();
        setIsLoading(true);
        setHasError(null);

        console.log('Dados do formulário:', formData);


        setIsLoading(false);
    };


    return (
        <div className="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl p-8 border border-white/20">
            <form className="space-y-6" onSubmit={handleSubmitForm}>
                <div className="space-y-2">
                    <label htmlFor="email" className="text-sm font-medium text-slate-700">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        className="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400"
                        placeholder="seu@email.com"
                        value={formData.email}
                        onChange={handleChangeForm}
                        // required 
                    />
                </div>

                <div className="space-y-2">
                    <label htmlFor="password" className="text-sm font-medium text-slate-700">
                        Senha
                    </label>
                    <input
                        type="password"
                        id="password"
                        className="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400"
                        placeholder="••••••••"
                        value={formData.password}
                        onChange={handleChangeForm}
                        // required 
                    />
                </div>

                {hasError && (
                    <div className="p-4 bg-red-50 border border-red-200 rounded-xl">
                        <p className="text-sm text-red-600 font-medium">{hasError}</p>
                    </div>
                )}

                <button
                    type="submit"
                    className="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition-colors duration-200 shadow-lg hover:shadow-xl"
                    disabled={isLoading}
                >
                    {isLoading ? 'Entrando...' : 'Entrar'}
                </button>
            </form>

            {/* <div className="mt-6 text-center text-sm text-slate-600">
                Não tem uma conta?{' '}
                <a href="#" className="text-blue-600 hover:text-blue-700 font-medium">
                    Cadastre-se
                </a>
            </div> */}
        </div>
    )
}