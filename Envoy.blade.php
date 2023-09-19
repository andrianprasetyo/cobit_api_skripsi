@servers(['web' => 'msi@10.11.11.18'])

@setup
    $repository = 'git@gitlab.millennia-solusi.xyz:cobit/cobit_api.git';
    $releases_dir = '/home/msi/app/releases';
    $app_dir = '/home/msi/app';
    $release = date('YmdHis');
    $new_release_dir = $releases_dir .'/'. $release;
@endsetup

@story('deploy')
    clone_repository
    run_composer
    update_symlinks
@endstory

@task('clone_repository')
    echo 'Cloning repository'
    [ -d {{ $releases_dir }} ] || mkdir {{ $releases_dir }}
    git clone {{ $repository }} {{ $new_release_dir }}
    cd {{ $new_release_dir }}
    git checkout dev
    git reset --hard {{ $commit }}
@endtask

@task('run_composer')
    echo "Starting deployment ({{ $release }})"
    cd {{ $new_release_dir }}
    composer install --no-plugins
    php artisan route:cache
    php artisan storage:link
@endtask

@task('update_symlinks')
    echo "Linking storage directory"
    rm -rf {{ $new_release_dir }}/storage
    ln -nfs {{ $app_dir }}/storage {{ $new_release_dir }}/storage

    echo 'Linking .env file'
    ln -nfs {{ $app_dir }}/.env {{ $new_release_dir }}/.env

    echo 'Linking current release'
    ln -nfs {{ $new_release_dir }} {{ $app_dir }}/current
@endtask
