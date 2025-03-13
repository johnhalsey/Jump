import {Head, Link} from '@inertiajs/react';

export default function Gravatar ({user}) {

    function gravatarUrl () {
        return user ? user.gravatar_url : "https://gravatar.com/avatar/27205e5c51cb03f862138b22bcb5dc20f94a342e744ff6df1b8dc8af3c865109?f=y&d=mp"
    }

    return (
        <>
            <div className={'min-w-[45px]'}>
                <img src={gravatarUrl()}
                     alt="Gravatar Image"
                     width="45px"
                     className={'rounded-full border border-gray-300'}
                />
            </div>
        </>
    );
}
