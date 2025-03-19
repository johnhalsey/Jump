import ApplicationLogo from '@/Components/ApplicationLogo';
import {Head, Link} from '@inertiajs/react';

export default function GuestLayout ({children}) {
    return (
        <>
            <div className="bg-gradient-to-br from-sky-50 to-white min-h-screen">
                <header className="p-3 shadow bg-white">
                    <nav className="container flex flex-1 mx-auto justify-between items-center">
                        <div className={'w-36'}>
                            <Link href={'/'}>
                                <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800"/>
                            </Link>
                        </div>

                        <div className={'align-middle'}>
                            <Link
                                href={route('login')}
                                className="border border-sky-600 hover:bg-sky-100 rounded-md px-3 py-2 text-gray-800
                                transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]
                                dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Log in
                            </Link>
                            <Link
                                href={route('register')}
                                className="ml-3 border border-sky-600 hover:bg-sky-100 hover:shadow rounded-md px-3 py-2
                                text-gray-800 ring-1 ring-transparent transition hover:text-black/70 focus:outline-none
                                focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Register
                            </Link>
                        </div>
                    </nav>
                </header>

                <main className="mt-10 px-3">

                    {children}

                </main>

                <footer className="py-16 text-center text-sm text-black dark:text-white/70">

                </footer>
            </div>

        </>
    );
}
