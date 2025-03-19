import {Head, Link} from '@inertiajs/react';
import ApplicationLogo from "@/Components/ApplicationLogo.jsx"
import GuestLayout from "@/Layouts/GuestLayout.jsx"

export default function Welcome ({}) {

    return (
        <GuestLayout>
            <Head title="Welcome"/>


            <div className={'grid grid-cols-1 lg:grid-cols-1 gap-8 md:gap-24 container mx-auto lg:pt-24'}>

                <div className={'text-5xl md:text-8xl col-span-1 font-light text-gray-700 text-center'}>
                    <h1>Project Management,</h1>
                    <h1>Simplified.</h1>
                </div>

                <div className={'col-span-1'}>
                    <div className={'p-3 rounded shadow bg-white'}>
                        <img className="max-w-full h-auto rounded" src={'/images/jump-project-view.png'}/>
                    </div>
                </div>

                <div className={'text-5xl md:text-8xl col-span-1 font-light text-gray-700 text-center'}>
                    <h1>Projects, Tasks, Simplicity,</h1>
                    <h1>All Unlimited.</h1>
                    <h1>All Free.</h1>
                </div>

                <div className={'col-span-1'}>
                    <div className={'p-3 rounded shadow bg-white'}>
                        <img className="max-w-full h-auto rounded" src={'/images/jump-dashboard.png'}/>
                    </div>
                </div>


            </div>
        </GuestLayout>
    )
}
