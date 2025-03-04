import {Head, Link} from '@inertiajs/react';

export default function Panel ({className, children}) {

    return (
        <>
            <div className={'border bg-white rounded shadow ' + className}>
                {children}
            </div>
        </>
    );
}
